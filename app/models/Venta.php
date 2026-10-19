<?php
require_once __DIR__ . "/../config/Database.php";

class Venta
{
    private $conn;
    public $error = '';

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    // El precio y el total se calculan aquí con los datos de la BD; no se usan los que envía el navegador
    public function registrarVenta($id_usuario, $id_cliente, $productos)
    {
        $this->error = '';

        if (empty($productos) || !is_array($productos)) {
            $this->error = 'El carrito está vacío.';
            return false;
        }

        // Agrupar cantidades por producto y rechazar cantidades no válidas
        $cantidades = [];
        foreach ($productos as $prod) {
            $id = filter_var($prod['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $cant = filter_var($prod['cantidad'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($id === false || $cant === false) {
                $this->error = 'Hay productos con una cantidad no válida en el carrito.';
                return false;
            }
            $cantidades[$id] = ($cantidades[$id] ?? 0) + $cant;
        }

        try {
            // INICIO DE TRANSACCIÓN (Todo o Nada)
            $this->conn->beginTransaction();

            // Bloquear las filas de los productos, verificar el stock y calcular precios
            $stmtCheck = $this->conn->prepare(
                "SELECT nombre, stock, precio_venta FROM productos WHERE id_producto = :prod AND estado = 1 FOR UPDATE"
            );
            $detalles = [];
            $totalCentavos = 0;
            foreach ($cantidades as $id => $cant) {
                $stmtCheck->execute([':prod' => $id]);
                $actual = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                if (!$actual) {
                    throw new RuntimeException("Un producto del carrito ya no existe o está inactivo.");
                }
                if ((int)$actual['stock'] < $cant) {
                    throw new RuntimeException("Stock insuficiente para \"{$actual['nombre']}\". Disponible: {$actual['stock']}, solicitado: {$cant}.");
                }

                // Se trabaja en centavos para evitar errores de redondeo
                $precioCentavos = (int)round($actual['precio_venta'] * 100);
                $subtotalCentavos = $precioCentavos * $cant;
                $totalCentavos += $subtotalCentavos;
                $detalles[] = [
                    'id' => $id,
                    'cantidad' => $cant,
                    'precio' => number_format($precioCentavos / 100, 2, '.', ''),
                    'subtotal' => number_format($subtotalCentavos / 100, 2, '.', ''),
                ];
            }
            $total = number_format($totalCentavos / 100, 2, '.', '');

            // A. Insertar Cabecera de Venta
            $sql = "INSERT INTO ventas (id_usuario, id_cliente, total, fecha, estado) 
                    VALUES (:user, :client, :total, NOW(), 'completada')";
            $query = $this->conn->prepare($sql);
            $query->bindParam(':user', $id_usuario);
            $query->bindParam(':client', $id_cliente);
            $query->bindParam(':total', $total);
            $query->execute();

            // Obtener el ID para el Ticket
            $id_venta = $this->conn->lastInsertId();

            // Preparar consultas para el bucle (Mejor rendimiento)
            $sqlDetalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) 
                           VALUES (:venta, :prod, :cant, :precio, :subtotal)";
            $stmtDetalle = $this->conn->prepare($sqlDetalle);

            // Consulta para RESTAR STOCK (nunca permite dejarlo en negativo)
            $sqlStock = "UPDATE productos SET stock = stock - :cant
                         WHERE id_producto = :prod AND stock >= :cant_min";
            $stmtStock = $this->conn->prepare($sqlStock);

            // B. Procesar cada producto del carrito
            foreach ($detalles as $prod) {
                $id = $prod['id'];
                $cant = $prod['cantidad'];

                // 1. Guardar Detalle
                $stmtDetalle->execute([
                    ':venta' => $id_venta,
                    ':prod' => $id,
                    ':cant' => $cant,
                    ':precio' => $prod['precio'],
                    ':subtotal' => $prod['subtotal'],
                ]);

                // 2. Restar Stock
                $stmtStock->execute([':cant' => $cant, ':prod' => $id, ':cant_min' => $cant]);
                if ($stmtStock->rowCount() === 0) {
                    throw new RuntimeException("No se pudo descontar el stock del producto #{$id}.");
                }
            }

            // CONFIRMAR TRANSACCIÓN
            $this->conn->commit();

            // Retornamos el ID para poder imprimir el ticket
            return $id_venta;
        } catch (Exception $e) {
            // Si algo falla, deshacemos todo
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            $this->error = ($e instanceof RuntimeException && !($e instanceof PDOException))
                ? $e->getMessage()
                : 'No se pudo registrar la venta. Intente nuevamente.';
            return false;
        }
    }
    public function obtenerDetallesVenta($id_venta)
    {
        try {
            $queryVenta = "SELECT v.*, 
                                  c.nombre as nombre_cliente, 
                                  c.documento as documento_cliente,
                                  u.nombre as nombre_vendedor
                           FROM ventas v
                           LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                           LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                           WHERE v.id_venta = :id
                           LIMIT 1";

            $stmt = $this->conn->prepare($queryVenta);
            $stmt->bindParam(':id', $id_venta);
            $stmt->execute();

            $venta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$venta) return false;
            $queryDetalles = "SELECT dv.cantidad, 
                                     dv.precio_unitario, 
                                     dv.subtotal, 
                                     p.nombre as nombre_producto,
                                     p.codigo_barras
                              FROM detalle_ventas dv
                              INNER JOIN productos p ON dv.id_producto = p.id_producto
                              WHERE dv.id_venta = :id";

            $stmtDetalles = $this->conn->prepare($queryDetalles);
            $stmtDetalles->bindParam(':id', $id_venta);
            $stmtDetalles->execute();

            $venta['productos'] = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

            return $venta;
        } catch (PDOException $e) {
            return false;
        }
    }
    /* =========================================================
       3. LISTADO HISTORIAL CON FILTROS
    ========================================================= */
    public function listarHistorial($fechaInicio = null, $fechaFin = null)
    {
        // Si no hay fechas, usamos el mes actual
        if (!$fechaInicio) $fechaInicio = date('Y-m-01');
        if (!$fechaFin) $fechaFin = date('Y-m-d');

        // Agregamos hora final al fechaFin para tomar todo el día (23:59:59)
        $fechaFinSql = $fechaFin . ' 23:59:59';

        $sql = "SELECT v.id_venta, v.fecha, v.total, v.estado, c.nombre as cliente 
                FROM ventas v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                WHERE v.fecha BETWEEN :inicio AND :fin
                ORDER BY v.fecha DESC";

        $query = $this->conn->prepare($sql);
        $query->bindParam(':inicio', $fechaInicio);
        $query->bindParam(':fin', $fechaFinSql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    // ... resto del modelo ...
    public function vaciarHistorial()
    {
        try {
            // 1. Iniciamos la transacción
            $this->conn->beginTransaction();

            // 2. Borramos los datos (DML)
            // Primero los hijos (detalles), luego los padres (ventas)
            $this->conn->exec("DELETE FROM detalle_ventas");
            $this->conn->exec("DELETE FROM ventas");

            // 3. Confirmamos el borrado
            $this->conn->commit();

            // 4. Reiniciamos los contadores (DDL)
            // Hacemos esto FUERA de la transacción porque ALTER TABLE hace un commit automático
            // Si esto falla, no importa tanto, los datos ya se borraron.
            try {
                $this->conn->exec("ALTER TABLE detalle_ventas AUTO_INCREMENT = 1");
                $this->conn->exec("ALTER TABLE ventas AUTO_INCREMENT = 1");
            } catch (Exception $e2) {
                // Ignoramos error de reset de ID, no es crítico
            }

            return true;
        } catch (Exception $e) {
            // SOLUCIÓN AL ERROR:
            // Solo hacemos rollback si la transacción sigue viva
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            return false;
        }
    }
}
