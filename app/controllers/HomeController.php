<?php
require_once __DIR__ . '/../models/Producto.php';

class HomeController {
    
    public function index() {
        // Traemos productos reales para mostrar en la portada
        $productoModel = new Producto();
        
        // Simulamos obtener "Destacados" (los últimos 3 agregados)
        // Puedes crear un método específico en el modelo si prefieres
        $todos = $productoModel->obtenerTodos();
        $destacados = array_slice($todos, 0, 4); // Tomamos solo 4

        require_once __DIR__ . "/../views/home/index.php";
    }
}
?>