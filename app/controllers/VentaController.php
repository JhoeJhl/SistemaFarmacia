<?php
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Venta.php';

class VentaController
{
    // Si alguien entra a ?controller=venta sin acción, lo mandamos al historial
    public function index()
    {
        $this->historial();
    }

    // Muestra la interfaz del POS (Punto de Venta)
    public function crear()
    {
        // Verificar sesión
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=index");
            exit;
        }

        // Cargamos modelos necesarios
        $productoModel = new Producto();
        $clienteModel = new Cliente();

        // Datos para la vista
        $productos = $productoModel->obtenerTodos(); // Para el buscador JS
        $clientes = $clienteModel->obtenerTodos();   // Para el select de clientes

        // Cargamos la vista de "Crear Venta"
        require_once __DIR__ . '/../views/ventas/crear.php';
    }

    // Procesa la venta enviada por POST
    public function guardar()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ventaModel = new Venta();

            $id_usuario = $_SESSION['user_id'];
            $id_cliente = $_POST['cliente_id'];

            // Decodificamos el JSON de productos que envía el JS (solo se usan el ID y la cantidad)
            $productos = json_decode($_POST['productos_json'] ?? '', true);

            // Capturamos el ID que retorna el modelo
            $id_venta = $ventaModel->registrarVenta($id_usuario, $id_cliente, is_array($productos) ? $productos : []);

            if ($id_venta) {
                // Éxito: Pasamos el ID por la URL para que el JS sepa qué ticket imprimir
                header("Location: index.php?controller=venta&action=crear&status=success&id_venta=" . $id_venta);
            } else {
                header("Location: index.php?controller=venta&action=crear&status=error&msg=" . urlencode($ventaModel->error));
            }
            exit;
        }
    }

    // Muestra el listado de ventas pasadas
    // ... dentro de VentaController ...

    public function historial()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=index");
            exit;
        }

        $ventaModel = new Venta();

        // Capturar filtros de la URL
        $fechaInicio = $_GET['desde'] ?? date('Y-m-01');
        $fechaFin = $_GET['hasta'] ?? date('Y-m-d');

        $ventas = $ventaModel->listarHistorial($fechaInicio, $fechaFin);

        require_once __DIR__ . '/../views/ventas/historial.php';
    }

    // NUEVO: Para ver los productos en el Modal sin recargar
    public function ajaxDetalle()
    {
        if (isset($_GET['id'])) {
            $ventaModel = new Venta();
            $datos = $ventaModel->obtenerDetallesVenta($_GET['id']);

            header('Content-Type: application/json');
            echo json_encode($datos);
            exit;
        }
    }
    // ... resto del controlador ...

    public function eliminarTodo()
    {
        // 1. Seguridad: Solo Admin
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Usamos el helper isAdmin() que creamos antes
        if (!function_exists('isAdmin') || !isAdmin()) {
            header("Location: index.php?controller=venta&action=historial&msg=error_permisos");
            exit;
        }

        // 2. Ejecutar borrado
        $ventaModel = new Venta();
        if ($ventaModel->vaciarHistorial()) {
            header("Location: index.php?controller=venta&action=historial&msg=history_cleared");
        } else {
            header("Location: index.php?controller=venta&action=historial&msg=error_db");
        }
    }
}
