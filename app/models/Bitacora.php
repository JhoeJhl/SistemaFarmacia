<?php
require_once __DIR__ . '/../config/Database.php';

class Bitacora {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }
    
    // Método estático para registrar (Crea su propia conexión rápida)
    public static function registrar($id_usuario, $accion, $tabla, $detalle) {
        $db = new Database();
        $conn = $db->conectar(); 

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        $sql = "INSERT INTO bitacora (id_usuario, accion, tabla_afectada, detalle, ip_usuario, fecha) 
                VALUES (:user, :accion, :tabla, :detalle, :ip, NOW())";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user', $id_usuario);
        $stmt->bindParam(':accion', $accion);
        $stmt->bindParam(':tabla', $tabla);
        $stmt->bindParam(':detalle', $detalle);
        $stmt->bindParam(':ip', $ip);
        
        return $stmt->execute();
    }

    // Método para leer la bitácora
    public function obtenerUltimos($limite = 50) {
        $sql = "SELECT b.*, u.nombre as usuario 
                FROM bitacora b 
                LEFT JOIN usuarios u ON b.id_usuario = u.id_usuario 
                ORDER BY b.fecha DESC LIMIT :limite";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // === MÉTODO CORREGIDO DE AUTO-LIMPIEZA ===
    public function limpiarAntiguos($dias = 60) {
        try {
            $fechaLimite = date('Y-m-d H:i:s', strtotime("-$dias days"));

            // 2. El SQL queda mucho más simple y compatible
            $sql = "DELETE FROM bitacora WHERE fecha < :fechaLimite";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':fechaLimite', $fechaLimite);
            
            return $stmt->execute();

        } catch (Exception $e) {
            return false;
        }
    }
}
?>