<?php ob_start(); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="h-[calc(100vh-100px)] flex flex-col md:flex-row gap-4 p-4 md:p-0">

    <div class="w-full md:w-2/3 flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-full">
        
        <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="ph ph-magnifying-glass absolute left-3 top-3 text-gray-400 text-xl"></i>
                <input type="text" id="buscador" placeholder="Buscar medicamento, código..." 
                       class="w-full pl-10 px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-500 outline-none transition-shadow text-sm"
                       onkeyup="filtrarProductos()">
            </div>
            <div class="flex gap-2 overflow-x-auto pb-1 sm:pb-0">
                <button onclick="filtrarCategoria('all')" class="px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 text-sm font-medium transition whitespace-nowrap active:bg-gray-200">Todo</button>
                <button onclick="filtrarCategoria('MEDICAMENTO')" class="px-4 py-2 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg hover:bg-blue-100 text-sm font-medium transition whitespace-nowrap active:bg-blue-200">Medicinas</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 bg-gray-50/50">
            <div id="grid-productos" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                </div>
        </div>
    </div>

    <div class="w-full md:w-1/3 flex flex-col bg-white rounded-xl shadow-xl border border-gray-200 h-full md:h-auto">
        
        <div class="p-5 bg-brand-900 text-white rounded-t-xl shrink-0">
            <h2 class="text-lg font-bold flex items-center gap-2">
                <i class="ph ph-receipt"></i> Nueva Venta
            </h2>
            <div class="mt-4">
                <label class="text-xs text-brand-200 uppercase font-bold">Cliente</label>
                <div class="flex gap-2 mt-1">
                    <select id="select-cliente" class="w-full bg-brand-800 border border-brand-700 text-white rounded p-2 text-sm focus:outline-none">
                        <option value="1">Cliente Casual / Público General</option>
                        <?php if(isset($clientes)): foreach($clientes as $c): ?>
                            <option value="<?= $c['id_cliente'] ?>"><?= $c['nombre'] ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                    <a href="index.php?controller=cliente&action=index" target="_blank" class="p-2 bg-brand-700 hover:bg-brand-600 rounded text-white" title="Nuevo Cliente">
                        <i class="ph ph-plus"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-2 min-h-[150px]" id="cart-items-container">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-2 py-2 w-1/2">Prod</th>
                        <th class="px-2 py-2 text-center">Cant</th>
                        <th class="px-2 py-2 text-right">Total</th>
                        <th class="px-2 py-2"></th>
                    </tr>
                </thead>
                <tbody id="cart-body" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-400">
                            <i class="ph ph-shopping-cart text-4xl mb-2"></i>
                            <p>Carrito vacío</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="p-5 bg-gray-50 border-t border-gray-200 shrink-0">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-500 text-sm">Subtotal</span>
                <span class="font-medium text-gray-700" id="lbl-subtotal">Bs. 0.00</span>
            </div>
            <div class="flex justify-between items-center mb-4">
                <span class="text-lg font-bold text-brand-900">Total a Pagar</span>
                <span class="text-2xl font-bold text-emerald-600" id="lbl-total">Bs. 0.00</span>
            </div>

            <form id="form-venta" action="index.php?controller=venta&action=guardar" method="POST">
                <input type="hidden" name="cliente_id" id="input-cliente">
                <input type="hidden" name="total_venta" id="input-total">
                <input type="hidden" name="productos_json" id="input-json">
                
                <button type="button" onclick="confirmarVentaModal()" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all flex justify-center items-center gap-2 text-lg group active:scale-95">
                    <i class="ph ph-currency-dollar text-2xl group-hover:scale-110 transition-transform"></i>
                    Cobrar
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    const productosDB = <?= json_encode($productos) ?>;
</script>

<script src="js/pos.js"></script>

<script>
    // Función mejorada para confirmar venta con modal de cobro
    function confirmarVentaModal() {
        // 1. Validar si hay productos
        if (typeof carrito === 'undefined' || carrito.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Carrito Vacío',
                text: 'Agrega productos antes de proceder al cobro.',
                confirmButtonColor: '#0ea5e9',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // 2. Calcular Total
        let total = carrito.reduce((sum, item) => sum + item.subtotal, 0);

        // 3. Mostrar Modal de Cobro con opciones de Factura
        Swal.fire({
            title: 'Resumen de Pago',
            html: `
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4 text-left">
                    <div class="flex justify-between text-lg font-bold text-gray-700 mb-3 border-b border-gray-200 pb-2">
                        <span>Total:</span>
                        <span class="text-brand-600">Bs. ${total.toFixed(2)}</span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-xs text-gray-500 font-bold uppercase block mb-1">Monto Recibido</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400">Bs.</span>
                            <input id="swal-input-cash" type="number" step="0.01" class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-brand-500 outline-none font-bold text-gray-800 text-lg" placeholder="0.00" oninput="calcularCambio(${total})">
                        </div>
                    </div>

                    <div class="flex justify-between text-sm font-bold text-gray-600 pt-2 border-t border-gray-200">
                        <span>Cambio:</span>
                        <span id="swal-cambio" class="text-gray-400 text-lg">Bs. 0.00</span>
                    </div>
                    
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="swal-check-factura" class="w-4 h-4 text-brand-600 rounded focus:ring-brand-500" checked>
                            <span class="text-sm text-gray-700 font-medium">Generar Factura / Ticket automáticamente</span>
                        </label>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Confirmar Pago',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#059669', // Verde éxito
            cancelButtonColor: '#d1d5db',
            focusConfirm: false,
            width: '400px',
            didOpen: () => {
                // Enfocar el input de efectivo automáticamente al abrir
                setTimeout(() => document.getElementById('swal-input-cash').focus(), 100);
            },
            preConfirm: () => {
                const cash = parseFloat(document.getElementById('swal-input-cash').value) || 0;
                const generarFactura = document.getElementById('swal-check-factura').checked;
                
                // Validación básica de monto
                if (cash < total) {
                    Swal.showValidationMessage(`Monto insuficiente. Faltan Bs. ${(total - cash).toFixed(2)}`);
                    return false;
                }
                return { cash, generarFactura };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;
                procesarEnvioVenta(total, data.generarFactura);
            }
        });
    }

    // Función para calcular cambio visualmente
    function calcularCambio(total) {
        const input = document.getElementById('swal-input-cash');
        const labelCambio = document.getElementById('swal-cambio');
        const recibido = parseFloat(input.value) || 0;
        const cambio = recibido - total;
        
        if (cambio >= 0) {
            labelCambio.innerText = 'Bs. ' + cambio.toFixed(2);
            labelCambio.className = 'text-emerald-600 text-lg font-bold';
        } else {
            labelCambio.innerText = 'Falta: Bs. ' + Math.abs(cambio).toFixed(2);
            labelCambio.className = 'text-red-500 text-lg font-bold';
        }
    }

    // Función para enviar los datos al servidor
    function procesarEnvioVenta(total, generarFactura) {
        document.getElementById('input-cliente').value = document.getElementById('select-cliente').value;
        document.getElementById('input-total').value = total;
        document.getElementById('input-json').value = JSON.stringify(carrito);
        Swal.fire({
            title: 'Procesando Venta',
            html: 'Guardando registro y actualizando stock...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });
        if(generarFactura) {
            const inputFactura = document.createElement('input');
            inputFactura.type = 'hidden';
            inputFactura.name = 'imprimir_ticket';
            inputFactura.value = '1';
            document.getElementById('form-venta').appendChild(inputFactura);
        }

        document.getElementById('form-venta').submit();
    }

    // --- MANEJO DE RETORNO (POST-VENTA) ---
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const idVenta = urlParams.get('id_venta');
        if (status === 'success' && idVenta) {
            Swal.fire({
                icon: 'success',
                title: '¡Venta Registrada!',
                text: 'La operación se completó correctamente.',
                showCancelButton: true,
                confirmButtonText: '<i class="ph-bold ph-printer"></i> Imprimir Factura',
                cancelButtonText: 'Cerrar',
                confirmButtonColor: '#0ea5e9',
                cancelButtonColor: '#64748b'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Abrir PDF en nueva pestaña
                    window.open(`index.php?controller=reporte&action=ticket&id=${idVenta}`, '_blank');
                }
                // Limpiar URL para evitar reenvíos o alertas dobles al recargar
                const cleanUrl = window.location.pathname + '?controller=venta&action=crear';
                window.history.replaceState({}, document.title, cleanUrl);
            });
        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'No se registró la venta',
                text: urlParams.get('msg') || 'Ocurrió un error al registrar la venta.',
                confirmButtonColor: '#0ea5e9'
            }).then(() => {
                const cleanUrl = window.location.pathname + '?controller=venta&action=crear';
                window.history.replaceState({}, document.title, cleanUrl);
            });
        }
    });
</script>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php'; 
?>