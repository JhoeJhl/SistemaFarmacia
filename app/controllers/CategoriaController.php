<?php
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController
{

    public function __construct()
    {
        // Validación de sesión básica para todo el controlador
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=index");
            exit;
        }
    }

    // Muestra la lista (Aquí estará el botón de "Nueva Categoría")
    public function index()
    {
        $categoriaModel = new Categoria();
        // Nota: Asegúrate que en tu modelo el método se llame 'obtenerTodos'
        $categorias = $categoriaModel->obtenerTodos();

        // Corregimos la ruta: apunta a app/views, no public
        require_once __DIR__ . '/../views/categorias/index.php';
    }
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new Categoria();
            $model->actualizar($_POST['id_categoria'], $_POST['nombre'], $_POST['descripcion']);
        }
        header("Location: index.php?controller=categoria&action=index");
        exit;
    }
    // Guarda una nueva categoría (Viene desde el Popup SweetAlert)
    public function guardar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nombre = $_POST["nombre"] ?? '';
            $desc = $_POST["descripcion"] ?? '';

            if (!empty($nombre)) {
                $categoria = new Categoria();
                $categoria->crear($nombre, $desc);
            }
        }
        // Redirección relativa correcta (sin la barra al inicio)
        header("Location: index.php?controller=categoria&action=index");
        exit;
    }

    // Eliminar categoría
    public function eliminar()
    {
        if (isset($_GET["id"])) {
            $categoria = new Categoria();
            $categoria->eliminar($_GET["id"]);
        }
        header("Location: index.php?controller=categoria&action=index");
        exit;
    }
}
