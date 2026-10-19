<?php
require_once __DIR__ . "/../models/Usuario.php";

class AuthController
{

    // Muestra la vista del login
    public function index()
    {
        // Si ya hay sesión, mandarlo al dashboard
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=dashboard&action=index");
            exit();
        }
        require_once __DIR__ . "/../views/auth/login.php";
    }

    // Procesa el formulario
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = $_POST['correo'] ?? '';
            $password = $_POST['password'] ?? '';

            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->login($correo, $password);

            if ($usuario) {
                // Iniciar Sesión
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['user_id'] = $usuario['id_usuario'];
                require_once __DIR__ . '/../models/Bitacora.php';
                Bitacora::registrar(
                    $usuario['id_usuario'],
                    'LOGIN',
                    'sesion',
                    "Inicio de sesión exitoso"
                );
                // ====================
                $_SESSION['user_name'] = $usuario['nombre'];
                $_SESSION['user_rol'] = $usuario['rol'];
                $_SESSION['user_image'] = $usuario['imagen'];

                // Redireccionar al Dashboard
                header("Location: index.php?controller=dashboard&action=index");
                exit();
            } else {
                // Error: Redirigir con alerta
                header("Location: index.php?controller=auth&action=index&error=1");
                exit();
            }
        }
    }

    // Cerrar Sesión
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: index.php?controller=auth&action=index");
        exit();
    }
}
