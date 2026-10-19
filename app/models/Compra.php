<?php
require_once __DIR__ . '/../config/Database.php';

class Compra {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    public function registrarCompra($id_usuario, $id_proveedor, $total, $productos) {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar Cabecera de Compra
            $query = "INSERT INTO compras (id_usuario, id_proveedor, total, fecha, estado) VALUES (:user, :prov, :total, NOW(), 'recibido')";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user', $id_usuario);
            $stmt->bindParam(':prov', $id_proveedor);
            $stmt->bindParam(':total', $total);
            $stmt->execute();
            
            $id_compra = $this->conn->lastInsertId();

            // 2. Insertar Detalles y AUMENTAR Stock
            $queryDetalle = "INSERT INTO detalle_compras (id_compra, id_producto, cantidad, costo_unitario, subtotal) 
                             VALUES (:compra, :prod, :cant, :costo, :subtotal)";
            $stmtDetalle = $this->conn->prepare($queryDetalle);

            // Ojo: Aquí sumamos (+) al stock
            $queryStock = "UPDATE productos SET stock = stock + :cant, precio_compra = :costo WHERE id_producto = :prod";
            $stmtStock = $this->conn->prepare($queryStock);

            foreach ($productos as $prod) {
                // Guardar detalle
                $stmtDetalle->bindParam(':compra', $id_compra);
                $stmtDetalle->bindParam(':prod', $prod['id']);
                $stmtDetalle->bindParam(':cant', $prod['cantidad']);
                $stmtDetalle->bindParam(':costo', $prod['precio']); // Costo de compra
                $stmtDetalle->bindParam(':subtotal', $prod['subtotal']);
                $stmtDetalle->execute();

                // Actualizar stock y actualizar el último precio de compra registrado
                $stmtStock->bindParam(':cant', $prod['cantidad']);
                $stmtStock->bindParam(':costo', $prod['precio']);
                $stmtStock->bindParam(':prod', $prod['id']);
                $stmtStock->execute();
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>