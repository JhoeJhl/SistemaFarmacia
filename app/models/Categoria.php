<?php
require_once __DIR__ . "/../config/Database.php";

class Categoria
{
    private $conn;
    private $table = "categorias";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    // Obtener todos
    public function obtenerTodos()
    {

        $query = "SELECT * FROM " . $this->table . " WHERE estado = 1 ORDER BY id_categoria DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear
    public function crear($nombre, $descripcion)
    {
        $sql = "INSERT INTO $this->table (nombre, descripcion) VALUES (?, ?)";
        $query = $this->conn->prepare($sql);
        return $query->execute([$nombre, $descripcion]);
    }

    // Buscar por ID
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM $this->table WHERE id_categoria = ?";
        $query = $this->conn->prepare($sql);
        $query->execute([$id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar
    public function actualizar($id, $nombre, $descripcion)
    {
        $query = "UPDATE " . $this->table . " SET nombre = :nom, descripcion = :desc WHERE id_categoria = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nombre);
        $stmt->bindParam(':desc', $descripcion);
        return $stmt->execute();
    }

    // Eliminar
    public function eliminar($id)
    {
        try {
            $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id_categoria = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}
