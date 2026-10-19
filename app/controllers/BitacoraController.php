<?php
require_once __DIR__ . "/../models/Bitacora.php";

class BitacoraController
{

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Seguridad
        if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'admin') {
            header("Location: index.php?controller=dashboard&action=index");
            exit;
        }

        $bitacoraModel = new Bitacora();
        $bitacoraModel->limpiarAntiguos(60);
        $logs = $bitacoraModel->obtenerUltimos(100);

        require_once __DIR__ . '/../views/bitacora/index.php';
    }
}
