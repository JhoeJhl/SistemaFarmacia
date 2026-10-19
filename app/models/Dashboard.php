<?php
require_once __DIR__ . '/../config/Database.php';

class Dashboard
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    // 1. Total Vendido Hoy
    public function totalVentasHoy()
    {
        $query = "SELECT SUM(total) as total FROM ventas WHERE DATE(fecha) = CURDATE() AND estado = 'completada'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // 2. Cantidad de Ventas Hoy
    public function cantidadVentasHoy()
    {
        $query = "SELECT COUNT(*) as cantidad FROM ventas WHERE DATE(fecha) = CURDATE() AND estado = 'completada'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['cantidad'] ?? 0;
    }

    // 3. Productos con Stock Bajo (Alerta Roja)
    public function obtenerStockBajo()
    {
        $query = "SELECT nombre, stock, stock_minimo, imagen FROM productos 
                  WHERE stock <= stock_minimo AND estado = 1 
                  ORDER BY stock ASC LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Medicamentos por Vencer (Próximos 30 días)
    public function obtenerPorVencer()
    {
        $query = "SELECT nombre, fecha_vencimiento, lote FROM productos 
                  WHERE tipo = 'MEDICAMENTO' 
                  AND fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) 
                  ORDER BY fecha_vencimiento ASC LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Últimas 5 Ventas (Para la tabla resumen)
    public function ultimasVentas()
    {
        $query = "SELECT v.id_venta, v.total, v.fecha, c.nombre as cliente 
                  FROM ventas v 
                  LEFT JOIN clientes c ON v.id_cliente = c.id_cliente 
                  WHERE v.estado = 'completada'
                  ORDER BY v.fecha DESC LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6. Datos para Gráfica (Ventas últimos 7 días)
    public function ventasUltimaSemana()
    {
        // CORRECCIÓN: Agregamos DATE(fecha) al GROUP BY y ordenamos por el valor real de la fecha
        $query = "SELECT DATE_FORMAT(fecha, '%d/%m') as fecha, SUM(total) as total 
                  FROM ventas 
                  WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                  GROUP BY DATE(fecha), DATE_FORMAT(fecha, '%d/%m')
                  ORDER BY DATE(fecha) ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // 7. Ganancia Neta de Hoy (Ventas - Costos)
    public function gananciaHoy()
    {
        // Calculamos: (Precio Venta - Precio Compra original) * Cantidad
        // NOTA: Esto asume que tienes 'precio_compra' en la tabla productos
        $sql = "SELECT SUM((dv.precio_unitario - p.precio_compra) * dv.cantidad) as ganancia
                FROM detalle_ventas dv
                INNER JOIN productos p ON dv.id_producto = p.id_producto
                INNER JOIN ventas v ON dv.id_venta = v.id_venta
                WHERE DATE(v.fecha) = CURDATE() AND v.estado = 'completada'";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['ganancia'] ?? 0;
    }
    public function contarTotalProductos()
    {
        $sql = "SELECT COUNT(*) as total FROM productos WHERE estado = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
