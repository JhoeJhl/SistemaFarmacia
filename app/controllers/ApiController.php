<?php
require_once __DIR__ . '/../models/Producto.php';

class ApiController {

    public function buscarProductos() {
        // Esto indica que la respuesta será JSON, no HTML
        header('Content-Type: application/json');

        $termino = $_GET['q'] ?? '';

        if (strlen($termino) < 2) {
            echo json_encode([]); // Si escribe menos de 2 letras, no buscar
            exit;
        }

        $productoModel = new Producto();
        // Debes tener un método 'buscarPorNombre' en tu modelo que use LIKE
        $resultados = $productoModel->buscarPorNombre($termino);

        echo json_encode($resultados);
    }
}
?>