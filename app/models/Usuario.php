<?php
require_once __DIR__ . '/../config/Database.php';

class Usuario {
    private $conn;
    private $table = 'usuarios';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    // Login (Ya lo tenías)
    public function login($correo, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE correo = :correo AND estado = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Si usas encriptación: if (password_verify($password, $user['password']))
            if ($password == $user['password']) { 
                unset($user['password']);
                return $user;
            }
        }
        return false;
    }

    // NUEVO: Obtener lista
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table . " WHERE estado = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // NUEVO: Crear Usuario
public function crear($nombre, $correo, $password, $rol) {
        try {
            $query = "INSERT INTO " . $this->table . " (nombre, correo, password, rol, estado) VALUES (:nom, :cor, :pass, :rol, 1)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':nom', $nombre);
            $stmt->bindParam(':cor', $correo);
            $stmt->bindParam(':pass', $password);
            $stmt->bindParam(':rol', $rol);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            // El código 1062 significa "Entrada Duplicada" en MySQL
            if ($e->errorInfo[1] == 1062) {
                return "duplicado"; 
            }
            // Otro error desconocido
            return false;
        }
    }

    // NUEVO: Eliminar
    public function eliminar($id) {
        $query = "UPDATE " . $this->table . " SET estado = 0 WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    // Actualizar Usuario (Inteligente con la contraseña)
    public function actualizar($id, $nombre, $correo, $rol, $password = null) {
        if (!empty($password)) {
            // Si hay contraseña nueva, actualizamos todo
            $query = "UPDATE " . $this->table . " 
                      SET nombre = :nom, correo = :cor, rol = :rol, password = :pass 
                      WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':pass', $password);
        } else {
            // Si NO hay contraseña, actualizamos solo datos personales
            $query = "UPDATE " . $this->table . " 
                      SET nombre = :nom, correo = :cor, rol = :rol 
                      WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($query);
        }

        $stmt->bindParam(':nom', $nombre);
        $stmt->bindParam(':cor', $correo);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
}
?>