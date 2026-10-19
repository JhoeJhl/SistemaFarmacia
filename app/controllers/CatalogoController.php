<?php
require_once __DIR__ . '/../models/Producto.php';

class CatalogoController {

    public function index() {
        $productoModel = new Producto();
        
        // Obtenemos todos los productos (puedes filtrar solo los que tienen stock > 0)
        $todos = $productoModel->obtenerTodos();
        
        // Filtramos solo los que tienen stock positivo para no frustrar al cliente
        $productos = array_filter($todos, function($p) {
            return $p['stock'] > 0;
        });

        require_once __DIR__ . "/../views/catalogo/index.php";
    }
}
?>