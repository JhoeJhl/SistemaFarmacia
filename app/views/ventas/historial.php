<?php ob_start(); ?>

<div class="max-w-7xl mx-auto">

    <div class="flex flex-col md:flex-row justify-between items-end mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Historial de Ventas</h1>
            <p class="text-gray-500 text-sm">Consulta transacciones y cierres de caja.</p>
        </div>

        <form action="index.php" method="GET" class="flex flex-col sm:flex-row gap-2 items-end bg-white p-2 rounded-xl shadow-sm border border-gray-100">
            <input type="hidden" name="controller" value="venta">
            <input type="hidden" name="action" value="historial">

            <div class="flex flex-col">
                <label class="text-[10px] text-gray-400 font-bold uppercase ml-1">Desde</label>
                <input type="date" name="desde" value="<?= $fechaInicio ?>" class="border border-gray-200 rounded-lg px-3 py-1 text-sm focus:outline-none focus:border-brand-500">
            </div>

            <div class="flex flex-col">
                <label class="text-[10px] text-gray-400 font-bold uppercase ml-1">Hasta</label>
                <input type="date" name="hasta" value="<?= $fechaFin ?>" class="border border-gray-200 rounded-lg px-3 py-1 text-sm focus:outline-none focus:border-brand-500">
            </div>

            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition-colors h-[34px]">
                Filtrar
            </button>
        </form>
    </div>

    <div class="flex justify-between items-center mb-4">
        <div class="relative w-full max-w-sm">
            <i class="ph ph-magnifying-glass absolute left-3 top-2.5 text-gray-400"></i>
            <input type="text" id="buscadorTabla" onkeyup="buscarEnTabla()" placeholder="Buscar por Ticket o Cliente..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-100">
        </div>

        <a href="index.php?controller=reporte&action=exportarExcel&desde=<?= $fechaInicio ?>&hasta=<?= $fechaFin ?>"
            class="text-green-600 hover:text-green-700 font-medium text-sm flex items-center gap-1 px-3 py-2 hover:bg-green-50 rounded-lg transition-colors border border-transparent hover:border-green-200"
            target="_blank">
            <i class="ph ph-microsoft-excel-logo text-lg"></i> Exportar Excel
        </a>
        <?php if (isAdmin()): ?>
            <button onclick="confirmarBorradoTotal()" class="text-red-600 hover:text-red-700 font-medium text-sm flex items-center gap-1 px-3 py-2 hover:bg-red-50 rounded-lg transition-colors ml-2">
                <i class="ph ph-trash text-lg"></i> Vaciar Historial
            </button>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" id="tablaVentas">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                    <tr>
                        <th class="p-4">Ticket</th>
                        <th class="p-4">Fecha</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Total</th>
                        <th class="p-4 text-center">Estado</th>
                        <th class="p-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($ventas)): ?>
                        <?php foreach ($ventas as $v): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="p-4 font-bold text-brand-600">
                                    #<?= str_pad($v['id_venta'], 5, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td class="p-4 text-gray-600">
                                    <?= date('d/m/Y', strtotime($v['fecha'])) ?>
                                    <span class="text-xs text-gray-400 ml-1"><?= date('H:i', strtotime($v['fecha'])) ?></span>
                                </td>
                                <td class="p-4 font-medium text-gray-800"><?= $v['cliente'] ?? 'Público General' ?></td>
                                <td class="p-4 font-bold text-gray-900">Bs. <?= number_format($v['total'], 2) ?></td>
                                <td class="p-4 text-center">
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full text-xs font-bold border border-emerald-200">
                                        Completada
                                    </span>
                                </td>
                                <td class="p-4 text-center flex justify-center gap-2">
                                    <button onclick="verDetalle(<?= $v['id_venta'] ?>)" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Ver Productos">
                                        <i class="ph ph-eye text-xl"></i>
                                    </button>
                                    <a href="index.php?controller=reporte&action=ticket&id=<?= $v['id_venta'] ?>" target="_blank" class="p-1.5 text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Reimprimir">
                                        <i class="ph ph-printer text-xl"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400">No hay ventas en este rango de fechas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-detalle" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0" id="modal-content">

        <div class="bg-brand-50 p-4 border-b border-brand-100 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-brand-900 text-lg">Detalle de Venta #<span id="modal-ticket"></span></h3>
                <p class="text-xs text-brand-600" id="modal-fecha"></p>
            </div>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600"><i class="ph ph-x text-2xl"></i></button>
        </div>

        <div class="p-0 overflow-y-auto max-h-[60vh]">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="p-3 pl-5">Producto</th>
                        <th class="p-3 text-center">Cant.</th>
                        <th class="p-3 text-right pr-5">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="modal-lista-productos" class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
            <span class="text-gray-500 font-medium">Total Pagado:</span>
            <span class="text-2xl font-bold text-brand-600" id="modal-total"></span>
        </div>
    </div>
</div>

<script>
    // 1. Función Búsqueda Rápida en Tabla (Sin recargar)
    function buscarEnTabla() {
        const texto = document.getElementById('buscadorTabla').value.toLowerCase();
        const filas = document.querySelectorAll('#tablaVentas tbody tr');

        filas.forEach(fila => {
            const contenido = fila.innerText.toLowerCase();
            fila.style.display = contenido.includes(texto) ? '' : 'none';
        });
    }

    // 2. Función Ver Detalle (AJAX)
    async function verDetalle(idVenta) {
        const modal = document.getElementById('modal-detalle');
        const content = document.getElementById('modal-content');

        // Abrir Modal
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);

        // Limpiar datos previos
        document.getElementById('modal-lista-productos').innerHTML = '<tr><td colspan="3" class="p-4 text-center">Cargando...</td></tr>';

        try {
            // Petición al controlador AJAX
            const response = await fetch(`index.php?controller=venta&action=ajaxDetalle&id=${idVenta}`);
            const data = await response.json();

            // Llenar Cabecera
            document.getElementById('modal-ticket').innerText = data.id_venta;
            document.getElementById('modal-fecha').innerText = data.fecha;

            // Llenar Productos
            const tbody = document.getElementById('modal-lista-productos');
            tbody.innerHTML = '';

            data.productos.forEach(p => {
                tbody.innerHTML += `
                    <tr>
                        <td class="p-3 pl-5">
                            <p class="font-bold text-gray-800">${p.nombre_producto}</p>
                            <p class="text-xs text-gray-400">${p.codigo_barras || ''}</p>
                        </td>
                        <td class="p-3 text-center text-gray-600">x${p.cantidad}</td>
                        <td class="p-3 text-right pr-5 font-bold text-gray-700">Bs. ${parseFloat(p.subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });

            // Llenar Total
            document.getElementById('modal-total').innerText = 'Bs. ' + parseFloat(data.total).toFixed(2);

        } catch (error) {
            console.error(error);
            document.getElementById('modal-lista-productos').innerHTML = '<tr><td colspan="3" class="p-4 text-center text-red-500">Error al cargar datos</td></tr>';
        }
    }

    function cerrarModal() {
        const modal = document.getElementById('modal-detalle');
        const content = document.getElementById('modal-content');

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function confirmarBorradoTotal() {
        Swal.fire({
            title: '¿ESTÁS SEGURO?',
            text: "Se eliminarán TODAS las ventas registradas. Esta acción no se puede deshacer y no afecta al stock actual.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Rojo
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, borrar todo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir a la acción destructiva
                window.location.href = "index.php?controller=venta&action=eliminarTodo";
            }
        });
    }

    // Alerta de éxito al volver
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('msg') === 'history_cleared') {
        Swal.fire('¡Eliminado!', 'El historial de ventas ha sido vaciado.', 'success');
        // Limpiar URL
        window.history.replaceState({}, document.title, "index.php?controller=venta&action=historial");
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>