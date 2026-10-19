<?php
require_once __DIR__ . "/../config/Database.php";

class Cliente {
    private $conn;
    private $table = "clientes";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    // Obtener todos los clientes
    public function obtenerTodos() {
        $sql = "SELECT * FROM " . $this->table;
        $query = $this->conn->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear cliente
    public function crear($nombre, $telefono, $email) {
        $sql = "INSERT INTO $this->table (nombre, telefono, email) VALUES (?, ?, ?)";
        $query = $this->conn->prepare($sql);
        return $query->execute([$nombre, $telefono, $email]);
    }

    // Buscar cliente por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM $this->table WHERE id_cliente = ?";
        $query = $this->conn->prepare($sql);
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar cliente
    public function actualizar($id, $nombre, $telefono, $email) {
        $sql = "UPDATE $this->table SET 
                    nombre=?, 
                    telefono=?, 
                    email=? 
                WHERE id_cliente=?";

        $query = $this->conn->prepare($sql);
        return $query->execute([$nombre, $telefono, $email, $id]);
    }

    // Eliminar cliente
    public function eliminar($id) {
        $sql = "DELETE FROM $this->table WHERE id_cliente=?";
        $query = $this->conn->prepare($sql);
        return $query->execute([$id]);
    }
}
