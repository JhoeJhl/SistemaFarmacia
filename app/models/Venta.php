<?php
require_once __DIR__ . "/../config/Database.php";

class Venta
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function registrarVenta($id_usuario, $id_cliente, $total, $productos)
    {
        try {
            // INICIO DE TRANSACCIÓN (Todo o Nada)
            $this->conn->beginTransaction();

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

            // Consulta para RESTAR STOCK
            $sqlStock = "UPDATE productos SET stock = stock - :cant WHERE id_producto = :prod";
            $stmtStock = $this->conn->prepare($sqlStock);

            // B. Procesar cada producto del carrito
            foreach ($productos as $prod) {
                // 1. Guardar Detalle
                $stmtDetalle->bindParam(':venta', $id_venta);
                $stmtDetalle->bindParam(':prod', $prod['id']); // Viene del JSON del POS
                $stmtDetalle->bindParam(':cant', $prod['cantidad']);
                $stmtDetalle->bindParam(':precio', $prod['precio']);
                $stmtDetalle->bindParam(':subtotal', $prod['subtotal']);
                $stmtDetalle->execute();

                // 2. Restar Stock
                $stmtStock->bindParam(':cant', $prod['cantidad']);
                $stmtStock->bindParam(':prod', $prod['id']);
                $stmtStock->execute();
            }

            // CONFIRMAR TRANSACCIÓN
            $this->conn->commit();

            // Retornamos el ID para poder imprimir el ticket
            return $id_venta;
        } catch (Exception $e) {
            // Si algo falla, deshacemos todo
            $this->conn->rollBack();
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
