<?php ob_start(); ?>

<div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Panel de Control</h1>
        <p class="text-gray-500 text-sm">Bienvenido al sistema, <?= $_SESSION['user_name'] ?? 'Usuario' ?>.</p>
    </div>
    <div class="flex gap-3">
        <a href="index.php?controller=compra&action=crear" class="px-4 py-2 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg hover:bg-purple-100 font-bold text-sm flex items-center gap-2 transition-colors">
            <i class="ph ph-arrow-down"></i> Entrada Stock
        </a>
        <a href="index.php?controller=venta&action=crear" class="px-4 py-2 bg-brand-600 text-white rounded-lg hover:bg-brand-700 font-bold text-sm flex items-center gap-2 shadow-md hover:shadow-lg transition-all">
            <i class="ph ph-shopping-cart"></i> Nueva Venta (POS)
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
        <div class="h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mr-4">
            <i class="ph ph-currency-dollar"></i>
        </div>
        <div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Ventas Hoy</p>
            <p class="text-2xl font-bold text-gray-900"><?= money($totalHoy) ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
        <div class="h-12 w-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-2xl mr-4">
            <i class="ph ph-trend-up"></i>
        </div>
        <div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Ganancia Est.</p>
            <p class="text-2xl font-bold text-gray-900"><?= money($gananciaHoy ?? 0) ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
        <div class="h-12 w-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl mr-4">
            <i class="ph ph-package"></i>
        </div>
        <div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Productos</p>
            <p class="text-2xl font-bold text-gray-900"><?= $totalProductos ?? 0 ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center hover:shadow-md transition-shadow">
        <div class="h-12 w-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-2xl mr-4">
            <i class="ph ph-calendar-x"></i>
        </div>
        <div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Por Vencer</p>
            <p class="text-2xl font-bold text-gray-900"><?= count($porVencer) ?></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-4">Tendencia de Ventas (7 días)</h3>
        <div class="relative h-72 w-full">
            <canvas id="ventasChart"></canvas>
        </div>
    </div>

    <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 overflow-y-auto max-h-[380px]">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="ph ph-siren text-red-500 text-xl"></i> Reposición Urgente
        </h3>

        <?php if (empty($stockBajo)): ?>
            <div class="text-center py-8 text-gray-400">
                <i class="ph ph-check-circle text-4xl mb-2 text-emerald-200"></i>
                <p class="text-sm">Inventario saludable.</p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($stockBajo as $prod): ?>
                    <div class="flex items-center justify-between border-b border-gray-50 pb-3 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-xs font-bold text-gray-400 overflow-hidden border border-gray-100">
                                <?= imagenProducto($prod, 'w-full h-full object-cover', 'text-xl') ?>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 line-clamp-1" title="<?= $prod['nombre'] ?>">
                                    <?= $prod['nombre'] ?>
                                </p>
                                <p class="text-xs text-red-500 font-bold bg-red-50 px-2 py-0.5 rounded-full inline-block mt-0.5">
                                    Quedan: <?= $prod['stock'] ?>
                                </p>
                            </div>
                        </div>
                        <a href="index.php?controller=compra&action=crear" class="text-xs font-medium text-brand-600 hover:text-brand-800 hover:bg-brand-50 px-2 py-1 rounded transition-colors">
                            Reponer
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h3 class="font-bold text-gray-800">Transacciones Recientes</h3>
            <p class="text-xs text-gray-400 mt-1">Cierre automático diario a las 00:00</p>
        </div>

        <div class="flex gap-2">
            <button onclick="cambiarPaginaTabla(-1)" id="btn-prev-tabla" class="p-1.5 border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50 transition">
                <i class="ph ph-caret-left"></i>
            </button>
            <span class="text-sm text-gray-500 self-center px-2" id="indicador-pagina">1</span>
            <button onclick="cambiarPaginaTabla(1)" id="btn-next-tabla" class="p-1.5 border border-gray-200 rounded hover:bg-gray-50 disabled:opacity-50 transition">
                <i class="ph ph-caret-right"></i>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto min-h-[300px]">
        <table class="w-full text-left text-sm" id="tabla-transacciones">
            <thead class="bg-gray-50 text-gray-500 font-medium uppercase text-xs">
                <tr>
                    <th class="p-4 pl-6">Ticket</th>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">Hora</th>
                    <th class="p-4">Total</th>
                    <th class="p-4 text-center">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="cuerpo-tabla">
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- 1. CONFIGURACIÓN GRÁFICA (Chart.js) ---
    const ctx = document.getElementById('ventasChart').getContext('2d');

    // Validamos que existan datos, si no, usamos arrays vacíos
    const etiquetas = <?= !empty($fechasGrafica) ? json_encode($fechasGrafica) : '[]' ?>;
    const datos = <?= !empty($totalesGrafica) ? json_encode($totalesGrafica) : '[]' ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Ventas (Bs)',
                data: datos,
                borderColor: '#0ea5e9',
                borderWidth: 3,
                backgroundColor: (context) => {
                    const ctx = context.chart.ctx;
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(14, 165, 233, 0.25)');
                    gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');
                    return gradient;
                },
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#0ea5e9',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [5, 5],
                        color: '#e2e8f0'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // --- 2. LÓGICA DE PAGINACIÓN TABLA (CARRUSEL) ---

    // Aseguramos que sea un array válido incluso si está vacío
    const ventasRaw = <?= !empty($ultimasVentas) ? json_encode($ultimasVentas) : '[]' ?>;
    const ventas = Array.isArray(ventasRaw) ? ventasRaw : [];

    const FILAS_POR_PAGINA = 6;
    let paginaTablaActual = 1;

    function renderizarTabla() {
        const tbody = document.getElementById('cuerpo-tabla');
        const indicador = document.getElementById('indicador-pagina');
        const btnPrev = document.getElementById('btn-prev-tabla');
        const btnNext = document.getElementById('btn-next-tabla');

        tbody.innerHTML = '';

        // Si no hay datos
        if (ventas.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="p-8 text-center text-gray-400">Sin movimientos recientes.</td></tr>`;
            indicador.innerText = '0 / 0';
            btnPrev.disabled = true;
            btnNext.disabled = true;
            return;
        }

        // Calcular índices
        const totalPaginas = Math.ceil(ventas.length / FILAS_POR_PAGINA);

        // Corrección de límites
        if (paginaTablaActual > totalPaginas) paginaTablaActual = totalPaginas;
        if (paginaTablaActual < 1) paginaTablaActual = 1;

        const inicio = (paginaTablaActual - 1) * FILAS_POR_PAGINA;
        const fin = inicio + FILAS_POR_PAGINA;
        const ventasPagina = ventas.slice(inicio, fin);

        // Actualizar indicador
        indicador.innerText = `${paginaTablaActual} / ${totalPaginas}`;
        btnPrev.disabled = paginaTablaActual === 1;
        btnNext.disabled = paginaTablaActual >= totalPaginas;

        // Dibujar filas
        ventasPagina.forEach(v => {
            // Formateo seguro de fecha
            let hora = '00:00';
            try {
                const fechaObj = new Date(v.fecha);
                hora = fechaObj.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {}

            const total = parseFloat(v.total).toFixed(2);
            const idTicket = String(v.id_venta).padStart(5, '0');
            const cliente = v.cliente || 'Público General';

            const fila = `
                <tr class="hover:bg-gray-50 transition-colors border-b border-gray-50 last:border-0">
                    <td class="p-4 pl-6 font-bold text-brand-700">#${idTicket}</td>
                    <td class="p-4 font-medium text-gray-700">${cliente}</td>
                    <td class="p-4 text-gray-500">${hora}</td>
                    <td class="p-4 font-bold text-gray-900">Bs. ${total}</td>
                    <td class="p-4 text-center">
                        <a href="index.php?controller=reporte&action=ticket&id=${v.id_venta}" 
                           target="_blank" 
                           class="text-gray-400 hover:text-brand-600 transition-colors p-2 hover:bg-brand-50 rounded-full inline-block" 
                           title="Imprimir Ticket">
                            <i class="ph ph-printer text-xl"></i>
                        </a>
                    </td>
                </tr>
            `;
            tbody.innerHTML += fila;
        });
    }

    function cambiarPaginaTabla(delta) {
        const totalPaginas = Math.ceil(ventas.length / FILAS_POR_PAGINA);
        const nuevaPagina = paginaTablaActual + delta;

        if (nuevaPagina >= 1 && nuevaPagina <= totalPaginas) {
            paginaTablaActual = nuevaPagina;
            renderizarTabla();
        }
    }

    // Iniciar Tabla al cargar
    document.addEventListener('DOMContentLoaded', renderizarTabla);
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>