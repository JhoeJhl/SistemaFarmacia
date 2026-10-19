<?php
require_once __DIR__ . "/../config/Database.php";

class Cliente {
    private $conn;
    private $table = "clientes";

    // Cliente "Público General" que usa el punto de venta por defecto
    const ID_PUBLICO_GENERAL = 1;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    // Obtener todos los clientes activos
    public function obtenerTodos() {
        $sql = "SELECT * FROM $this->table WHERE estado = 1 ORDER BY id_cliente = " . self::ID_PUBLICO_GENERAL . " DESC, nombre ASC";
        $query = $this->conn->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear cliente
    // La tabla tiene las columnas "correo" y "email"; se guardan ambas para mantenerlas iguales
    public function crear($documento, $nombre, $telefono, $correo, $direccion) {
        $sql = "INSERT INTO $this->table (documento, nombre, telefono, correo, email, direccion, estado)
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $query = $this->conn->prepare($sql);
        return $query->execute([$documento, $nombre, $telefono, $correo, $correo, $direccion]);
    }

    // Buscar cliente por ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM $this->table WHERE id_cliente = ?";
        $query = $this->conn->prepare($sql);
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar cliente
    public function actualizar($id, $documento, $nombre, $telefono, $correo, $direccion) {
        $sql = "UPDATE $this->table SET
                    documento = ?,
                    nombre = ?,
                    telefono = ?,
                    correo = ?,
                    email = ?,
                    direccion = ?
                WHERE id_cliente = ?";

        $query = $this->conn->prepare($sql);
        return $query->execute([$documento, $nombre, $telefono, $correo, $correo, $direccion, $id]);
    }

    // Eliminar cliente (borrado lógico: sus ventas siguen apuntando a él)
    public function eliminar($id) {
        if ((int)$id === self::ID_PUBLICO_GENERAL) {
            return false;
        }
        $sql = "UPDATE $this->table SET estado = 0 WHERE id_cliente = ?";
        $query = $this->conn->prepare($sql);
        return $query->execute([$id]);
    }
}
