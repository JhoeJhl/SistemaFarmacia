<?php
require_once __DIR__ . '/../config/Database.php';

class Proveedor {
    private $conn;
    private $table = 'proveedores';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table . " WHERE estado = 1 ORDER BY id_proveedor DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $telefono, $correo, $direccion) {
        $query = "INSERT INTO " . $this->table . " (nombre, telefono, correo, direccion, estado) VALUES (:nom, :tel, :cor, :dir, 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nombre);
        $stmt->bindParam(':tel', $telefono);
        $stmt->bindParam(':cor', $correo);
        $stmt->bindParam(':dir', $direccion);
        return $stmt->execute();
    }

    public function eliminar($id) {
        // Borrado lógico (no borramos el dato, solo lo ocultamos)
        $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id_proveedor = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>