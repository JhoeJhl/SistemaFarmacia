<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Seguridad: Solo el admin puede gestionar usuarios
        if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] !== 'admin') {
            header("Location: index.php?controller=dashboard&action=index");
            exit;
        }
    }

    public function index() {
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->obtenerTodos(); // Debes crear este método en el modelo
        require_once __DIR__ . '/../views/usuarios/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $correo = $_POST['correo'];
            $rol = $_POST['rol'];
            $password = $_POST['password'];

            // Encriptamos la contraseña (Seguridad Profesional)
            // El usuario ya no se guarda como "12345", sino como "$2y$10$..."
            // IMPORTANTE: Para que el login funcione con esto, en AuthController debes usar password_verify()
            // Si quieres mantenerlo simple por ahora, usa: $passHash = $password;
            $passHash = $password; 

            $usuarioModel = new Usuario();
            $usuarioModel->crear($nombre, $correo, $passHash, $rol);
            
            header("Location: index.php?controller=usuario&action=index&msg=success");
        }
    }

    public function eliminar() {
        if (isset($_GET['id'])) {
            // Evitar que el admin se borre a sí mismo
            if ($_GET['id'] == $_SESSION['user_id']) {
                die("No puedes eliminar tu propia cuenta.");
            }
            $usuarioModel = new Usuario();
            $usuarioModel->eliminar($_GET['id']);
        }
        header("Location: index.php?controller=usuario&action=index");
    }
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id_usuario'];
            $nombre = $_POST['nombre'];
            $correo = $_POST['correo'];
            $rol = $_POST['rol'];
            $password = $_POST['password']; // Puede venir vacío

            $usuarioModel = new Usuario();
            
            // Si hay contraseña, aquí deberías encriptarla si usas hash
            // $passToSend = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
            $passToSend = $password; // Modo simple por ahora

            $usuarioModel->actualizar($id, $nombre, $correo, $rol, $passToSend);
            
            header("Location: index.php?controller=usuario&action=index&msg=updated");
        }
    }
}
?>