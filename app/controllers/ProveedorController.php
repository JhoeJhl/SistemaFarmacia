<?php
require_once __DIR__ . '/../models/Proveedor.php';

class ProveedorController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=index");
            exit;
        }
    }

    public function index() {
        $model = new Proveedor();
        $proveedores = $model->obtenerTodos();
        require_once __DIR__ . '/../views/proveedores/index.php';
    }

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $correo = $_POST['correo'];
            $direccion = $_POST['direccion'];

            $model = new Proveedor();
            $model->crear($nombre, $telefono, $correo, $direccion);
        }
        header("Location: index.php?controller=proveedor&action=index");
        exit;
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_var($_POST['id_proveedor'] ?? null, FILTER_VALIDATE_INT);
            $nombre = trim($_POST['nombre'] ?? '');

            if ($id !== false && $nombre !== '') {
                $model = new Proveedor();
                $model->actualizar($id, $nombre, $_POST['telefono'] ?? '', $_POST['correo'] ?? '', $_POST['direccion'] ?? '');
            }
        }
        header("Location: index.php?controller=proveedor&action=index");
        exit;
    }

    public function eliminar() {
        if (isset($_GET['id'])) {
            $model = new Proveedor();
            $model->eliminar($_GET['id']);
        }
        header("Location: index.php?controller=proveedor&action=index");
        exit;
    }
}
?>