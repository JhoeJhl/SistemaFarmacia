<?php
require_once __DIR__ . '/../config/Database.php';

class BackupController {

    public function descargar() {
        // 1. Seguridad: Solo Administradores
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Usamos la función helper si existe, sino validación manual
        if ((function_exists('isAdmin') && !isAdmin()) || (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin')) {
            die("Acceso denegado. Solo administradores pueden realizar copias de seguridad.");
        }

        // 2. Configuración
        $db = new Database();
        $conn = $db->conectar();
        
        // Obtener nombre de la base de datos actual
        $dbName = $conn->query('SELECT DATABASE()')->fetchColumn();
        $fecha = date('Y-m-d_H-i');
        $filename = "Respaldo_{$dbName}_{$fecha}.sql";

        // 3. Encabezados del archivo SQL
        $content = "-- RESPALDO AUTOMÁTICO - SISTEMA FARMAVIDA\n";
        $content .= "-- Fecha: " . date('d/m/Y H:i:s') . "\n";
        $content .= "-- Base de datos: " . $dbName . "\n\n";
        $content .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        try {
            // 4. Obtener todas las tablas
            $tables = [];
            $result = $conn->query("SHOW TABLES");
            while($row = $result->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            // 5. Recorrer tablas
            foreach($tables as $table) {
                // A. Estructura (CREATE TABLE)
                $row = $conn->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
                $content .= "\n\n-- Estructura de la tabla `$table` --\n";
                $content .= "DROP TABLE IF EXISTS `$table`;\n";
                $content .= $row[1] . ";\n\n";

                // B. Datos (INSERT INTO)
                $rows = $conn->query("SELECT * FROM `$table`");
                $num_fields = $rows->columnCount();
                
                if($rows->rowCount() > 0){
                    $content .= "-- Volcado de datos para la tabla `$table` --\n";
                }

                while($r = $rows->fetch(PDO::FETCH_NUM)) {
                    $content .= "INSERT INTO `$table` VALUES(";
                    for($j=0; $j<$num_fields; $j++) {
                        // Manejo de NULL y comillas
                        if(!isset($r[$j])) { 
                            $content .= "NULL"; 
                        } else {
                            $r[$j] = addslashes($r[$j]);
                            $r[$j] = str_replace("\n","\\n",$r[$j]);
                            $content .= "'" . $r[$j] . "'";
                        }
                        if ($j < ($num_fields-1)) { $content .= ','; }
                    }
                    $content .= ");\n";
                }
            }
            
            $content .= "\nSET FOREIGN_KEY_CHECKS=1;";

            // 6. Forzar Descarga
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . $filename . "\"");
            echo $content;
            exit;

        } catch (Exception $e) {
            die("Error al generar backup: " . $e->getMessage());
        }
    }
}
?>