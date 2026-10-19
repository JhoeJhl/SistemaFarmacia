<?php
require_once __DIR__ . '/../config/Database.php';

class Venta {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    // Registra la venta completa (Cabecera + Detalles)
    public function registrarVenta($id_usuario, $id_cliente, $total, $productos) {
        try {
            // 1. Iniciar Transacción (Todo o nada)
            $this->conn->beginTransaction();

            // 2. Insertar Cabecera (Tabla 'ventas')
            $query = "INSERT INTO ventas (id_usuario, id_cliente, total, fecha) VALUES (:user, :client, :total, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user', $id_usuario);
            $stmt->bindParam(':client', $id_cliente);
            $stmt->bindParam(':total', $total);
            $stmt->execute();
            
            // Obtener el ID de la venta recién creada
            $id_venta = $this->conn->lastInsertId();

            // 3. Insertar Detalles y Restar Stock
            $queryDetalle = "INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario, subtotal) 
                             VALUES (:venta, :prod, :cant, :precio, :subtotal)";
            $stmtDetalle = $this->conn->prepare($queryDetalle);

            $queryStock = "UPDATE productos SET stock = stock - :cant WHERE id_producto = :prod";
            $stmtStock = $this->conn->prepare($queryStock);

            foreach ($productos as $prod) {
                // Guardar detalle
                $stmtDetalle->bindParam(':venta', $id_venta);
                $stmtDetalle->bindParam(':prod', $prod['id']);
                $stmtDetalle->bindParam(':cant', $prod['cantidad']);
                $stmtDetalle->bindParam(':precio', $prod['precio']);
                $stmtDetalle->bindParam(':subtotal', $prod['subtotal']);
                $stmtDetalle->execute();

                // Restar stock
                $stmtStock->bindParam(':cant', $prod['cantidad']);
                $stmtStock->bindParam(':prod', $prod['id']);
                $stmtStock->execute();
            }

            // 4. Confirmar Transacción
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Si algo falla, deshacer todo
            $this->conn->rollBack();
            return false;
        }
    }
}
?>