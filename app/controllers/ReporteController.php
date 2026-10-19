<?php
// 1. Cargamos el Modelo de Venta (para datos)
require_once __DIR__ . '/../models/Venta.php';

// 2. Cargamos la Librería FPDF
require_once __DIR__ . "/../lib/fpdf.php";

class ReporteController
{

    public function ticket()
    {
        // 1. Validar ID
        if (!isset($_GET['id'])) {
            die("Error: No se especificó una venta.");
        }

        $id_venta = $_GET['id'];

        // 2. Obtener datos
        $ventaModel = new Venta();
        $venta = $ventaModel->obtenerDetallesVenta($id_venta);

        if (!$venta) {
            die("Error: Venta no encontrada.");
        }

        // ==========================================
        //       CONFIGURACIÓN DEL PDF (TICKET)
        // ==========================================

        // 80mm de ancho (Estándar Térmico) y altura variable (aprox 220mm para empezar)
        $pdf = new FPDF('P', 'mm', array(80, 260));
        $pdf->AddPage();
        $pdf->SetMargins(4, 4, 4);
        $pdf->SetAutoPageBreak(false);

        // --- ENCABEZADO ---
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(72, 8, 'FARMA PLUS', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(72, 5, 'Cuidamos tu Salud', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(72, 4, 'NIT: 900800700', 0, 1, 'C');
        $pdf->Cell(72, 4, 'Av. Panamericana #500 - El Alto', 0, 1, 'C');
        $pdf->Cell(72, 4, 'Tel: (2) 280-0000', 0, 1, 'C');

        // Línea separadora decorativa
        $pdf->Ln(2);
        $pdf->Cell(72, 0, '---------------------------------------------------', 0, 1, 'C');
        $pdf->Ln(2);

        // --- DATOS DE LA FACTURA ---
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(72, 5, 'FACTURA DE VENTA', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);

        $pdf->Cell(20, 5, 'Nro Ticket:', 0, 0);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(52, 5, '# ' . str_pad($venta['id_venta'], 6, '0', STR_PAD_LEFT), 0, 1, 'R');

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(20, 5, 'Fecha:', 0, 0);
        $pdf->Cell(52, 5, date('d/m/Y H:i:s', strtotime($venta['fecha'])), 0, 1, 'R');

        $pdf->Cell(20, 5, 'Cliente:', 0, 0);
        // Recortamos el nombre si es muy largo para que no rompa el diseño
        $nombreCliente = utf8_decode(substr($venta['nombre_cliente'], 0, 25));
        $pdf->Cell(52, 5, $nombreCliente, 0, 1, 'R');

        $pdf->Ln(2);
        $pdf->Cell(72, 0, '---------------------------------------------------', 0, 1, 'C');
        $pdf->Ln(2);

        // --- DETALLE DE PRODUCTOS ---
        // Encabezados de tabla
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(34, 4, 'PRODUCTO', 0, 0, 'L');
        $pdf->Cell(8, 4, 'CANT', 0, 0, 'C');
        $pdf->Cell(14, 4, 'P.UNIT', 0, 0, 'R');
        $pdf->Cell(16, 4, 'TOTAL', 0, 1, 'R');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', '', 7);

        foreach ($venta['productos'] as $prod) {
            $nombre = utf8_decode(substr($prod['nombre_producto'], 0, 22)); // Nombre corto
            $precio = number_format($prod['precio_unitario'], 2);
            $subtotal = number_format($prod['subtotal'], 2);

            $pdf->Cell(34, 4, $nombre, 0, 0, 'L');
            $pdf->Cell(8, 4, $prod['cantidad'], 0, 0, 'C');
            $pdf->Cell(14, 4, $precio, 0, 0, 'R');
            $pdf->Cell(16, 4, $subtotal, 0, 1, 'R');
            $pdf->Ln(4);
        }

        $pdf->Ln(2);
        $pdf->Cell(72, 0, '---------------------------------------------------', 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(45, 8, 'TOTAL A PAGAR:', 0, 0, 'R');
        $pdf->Cell(27, 8, 'Bs. ' . number_format($venta['total'], 2), 0, 1, 'R');
        $pdf->Ln(10);
        $qrData = "FACTURA-FARMAPLUS-" . $venta['id_venta'] . "-TOTAL-" . $venta['total'];
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);

        // Insertamos la imagen centrada
        // (x=25, y=posición actual, w=30, h=30)
        $pdf->Image($qrUrl, 25, $pdf->GetY(), 30, 30, 'PNG');

        // Movemos el cursor abajo del QR
        $pdf->Ln(32);

        // --- PIE DE PÁGINA ---
        $pdf->SetFont('Arial', '', 7);
        $pdf->Cell(72, 4, utf8_decode('CÓDIGO DE CONTROL: A1-B2-C3-D4'), 0, 1, 'C');
        $pdf->Ln(1);
        $pdf->SetFont('Arial', 'I', 7);
        $pdf->MultiCell(72, 3, utf8_decode("Gracias por su preferencia.\n" .
            "Cualquier reclamo.\n" .
            "Visítenos en www.farmaplus.com.bo"), 0, 'C');

        // --- NOMBRE DEL ARCHIVO ---
        // 'I' = Inline (Ver en navegador), 'D' = Download (Descargar directo)
        $nombreArchivo = 'Factura_Venta_' . str_pad($venta['id_venta'], 5, '0', STR_PAD_LEFT) . '.pdf';
        $pdf->Output('I', $nombreArchivo);
    }
    // ... dentro de ReporteController ...

    // NUEVA FUNCIÓN: Exportar a Excel (CSV)
    public function exportarExcel()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) exit;

        // 1. Capturar filtros de la URL (igual que en el historial)
        $fechaInicio = $_GET['desde'] ?? date('Y-m-01');
        $fechaFin = $_GET['hasta'] ?? date('Y-m-d');

        // 2. Obtener datos del modelo
        require_once __DIR__ . '/../models/Venta.php';
        $ventaModel = new Venta();
        $ventas = $ventaModel->listarHistorial($fechaInicio, $fechaFin);

        // Calcular totales
        $totalGeneral = 0;
        $totalVentas = count($ventas);
        $ventasPagadas = 0;
        $ventasPendientes = 0;

        foreach ($ventas as $v) {
            $totalGeneral += $v['total'];
            if ($v['estado'] === 'pagado') $ventasPagadas++;
            if ($v['estado'] === 'pendiente') $ventasPendientes++;
        }

        // 3. Configurar cabeceras para descarga como HTML/Excel
        $filename = "Reporte_Ventas_" . date('Ymd_His') . ".xls";
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // 4. Crear HTML con formato Excel
        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        echo '<style>';
        echo '
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #4CAF50; color: white; font-weight: bold; padding: 8px; text-align: center; }
        td { padding: 6px; border: 1px solid #ddd; }
        .titulo { font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 10px; }
        .subtitulo { font-size: 14px; color: #7f8c8d; margin-bottom: 15px; }
        .resumen { background-color: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .total { background-color: #e8f5e9; font-weight: bold; }
        .pagado { color: #27ae60; font-weight: bold; }
        .pendiente { color: #e74c3c; font-weight: bold; }
        .cancelado { color: #95a5a6; font-weight: bold; }
        .encabezado { background-color: #3498db; color: white; padding: 10px; text-align: center; font-size: 16px; }
        .numero { text-align: right; }
        .centrado { text-align: center; }
    ';
        echo '</style>';
        echo '</head>';
        echo '<body>';

        // ENCABEZADO DEL REPORTE
        echo '<div class="encabezado">';
        echo 'REPORTE DE VENTAS';
        echo '</div>';

        echo '<div class="subtitulo">';
        echo 'Generado: ' . date('d/m/Y H:i:s') . ' | Período: ' .
            date('d/m/Y', strtotime($fechaInicio)) . ' - ' .
            date('d/m/Y', strtotime($fechaFin));
        echo '</div>';

        // RESUMEN ESTADÍSTICO
        echo '<div class="resumen">';
        echo '<strong>RESUMEN:</strong><br>';
        echo 'Total Ventas: ' . $totalVentas . ' | ';
        echo 'Pagadas: <span class="pagado">' . $ventasPagadas . '</span> | ';
        echo 'Pendientes: <span class="pendiente">' . $ventasPendientes . '</span> | ';
        echo 'Total General: <strong>Bs ' . number_format($totalGeneral, 2) . '</strong>';
        echo '</div>';

        // TABLA DE DATOS
        echo '<table border="1">';

        // CABECERAS CON COLORES
        echo '<thead>';
        echo '<tr>';
        echo '<th style="background-color: #2c3e50; color: white;">N°</th>';
        echo '<th style="background-color: #2c3e50; color: white;">ID Venta</th>';
        echo '<th style="background-color: #2c3e50; color: white;">Fecha</th>';
        echo '<th style="background-color: #2c3e50; color: white;">Hora</th>';
        echo '<th style="background-color: #2c3e50; color: white;">Cliente</th>';
        echo '<th style="background-color: #2c3e50; color: white;">Total (Bs)</th>';
        echo '<th style="background-color: #2c3e50; color: white;">Estado</th>';
        echo '</tr>';
        echo '</thead>';

        echo '<tbody>';

        // DATOS CON FORMATO CONDICIONAL
        $contador = 1;
        foreach ($ventas as $v) {
            // Determinar clase CSS según estado
            $claseEstado = strtolower($v['estado']);

            echo '<tr>';
            echo '<td class="centrado">' . $contador++ . '</td>';
            echo '<td class="centrado">' . $v['id_venta'] . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($v['fecha'])) . '</td>';
            echo '<td class="centrado">' . date('H:i:s', strtotime($v['fecha'])) . '</td>';
            echo '<td>' . htmlspecialchars($v['cliente'] ?? 'Público General') . '</td>';
            echo '<td class="numero" style="background-color: ' . ($v['total'] > 1000 ? '#e8f5e9' : '#fff') . '">';
            echo 'Bs ' . number_format($v['total'], 2);
            echo '</td>';

            // Estado con colores
            echo '<td class="centrado ' . $claseEstado . '">';
            echo ucfirst($v['estado']);
            echo '</td>';

            echo '</tr>';
        }

        // FILA DE TOTAL GENERAL
        echo '<tr class="total">';
        echo '<td colspan="5" style="text-align: right; background-color: #2c3e50; color: white;">';
        echo '<strong>TOTAL GENERAL:</strong>';
        echo '</td>';
        echo '<td class="numero" style="background-color: #27ae60; color: white;">';
        echo '<strong>Bs ' . number_format($totalGeneral, 2) . '</strong>';
        echo '</td>';
        echo '<td style="background-color: #2c3e50;"></td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';

        // PIE DE PÁGINA
        echo '<div style="margin-top: 20px; color: #7f8c8d; font-size: 12px; text-align: center;">';
        echo 'Reporte generado automáticamente por el sistema | Total de registros: ' . $totalVentas;
        echo '</div>';

        echo '</body>';
        echo '</html>';

        exit;
    }
}
