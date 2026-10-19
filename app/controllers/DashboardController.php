<?php
// Importamos el modelo que acabas de compartir
require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController {

    public function index() {
        // 1. Seguridad de sesión
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=index");
            exit;
        }

        // 2. Instanciar el Modelo
        $dashboard = new Dashboard();

        // 3. Obtener los datos usando TUS métodos exactos
        $totalHoy       = $dashboard->totalVentasHoy();
        $cantidadVentas = $dashboard->cantidadVentasHoy();
        $stockBajo      = $dashboard->obtenerStockBajo();
        $porVencer      = $dashboard->obtenerPorVencer();
        $ultimasVentas  = $dashboard->ultimasVentas();
        $datosGrafica   = $dashboard->ventasUltimaSemana();
        $gananciaHoy    = $dashboard->gananciaHoy();
        $totalProductos = $dashboard->contarTotalProductos();
        // 4. Preparar datos para Chart.js (Separar en dos arrays simples)
        // Esto convierte: [{'fecha':'10/01', 'total':500}, ...] 
        // a: etiquetas=['10/01', ...] y totales=[500, ...]
        $fechasGrafica = [];
        $totalesGrafica = [];

        if (!empty($datosGrafica)) {
            foreach($datosGrafica as $dia) {
                $fechasGrafica[] = $dia['fecha'];
                $totalesGrafica[] = $dia['total'];
            }
        }

        // 5. Cargar la vista
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
?>