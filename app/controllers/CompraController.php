<?php
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Proveedor.php';
require_once __DIR__ . '/../models/Compra.php';
require_once __DIR__ . '/../models/Bitacora.php';

class CompraController
{

    // Mostrar formulario de nueva compra
    public function crear()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=index");
            exit;
        }

        $productoModel = new Producto();
        $proveedorModel = new Proveedor();

        $productos = $productoModel->obtenerTodos();
        $proveedores = $proveedorModel->obtenerTodos(); // Asegúrate de tener este modelo

        require_once __DIR__ . '/../views/compras/crear.php';
    }

    // Guardar la compra
    public function guardar()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $compraModel = new Compra();

            $id_usuario = $_SESSION['user_id'];
            $id_proveedor = $_POST['proveedor_id'];
            $total = $_POST['total_compra'];
            $productos = json_decode($_POST['productos_json'], true);

            // 2. Intentamos registrar la compra PRIMERO
            if ($compraModel->registrarCompra($id_usuario, $id_proveedor, $total, $productos)) {

                // 3. Si funcionó, ENTONCES registramos en la Bitácora
                // (Así solo guardamos acciones reales)
                Bitacora::registrar(
                    $_SESSION['user_id'],
                    'INGRESO',
                    'compras',
                    "Ingreso de mercadería por valor de Bs. $total"
                );

                header("Location: index.php?controller=producto&action=index&msg=stock_actualizado");
            } else {
                header("Location: index.php?controller=compra&action=crear&error=true");
            }
        }
    }
}
