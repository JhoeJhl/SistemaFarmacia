<?php ob_start(); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-7xl mx-auto">

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">Reabastecimiento
            </h1>
            <p class="text-gray-500 text-sm">Registra facturas de compra y actualiza el stock.</p>
        </div>

        <div class="w-full md:w-auto bg-white p-2 rounded-lg border border-gray-200 shadow-sm flex items-center gap-2">
            <span class="text-xs font-bold text-gray-500 uppercase px-2">Proveedor:</span>
            <select id="select-proveedor" class="bg-transparent text-sm font-medium text-gray-800 focus:outline-none cursor-pointer">
                <?php foreach ($proveedores as $prov): ?>
                    <option value="<?= $prov['id_proveedor'] ?>"><?= $prov['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Agregar Producto</h3>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                    <div class="md:col-span-6 relative">
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Buscar Producto</label>
                        <i class="ph ph-magnifying-glass absolute left-3 top-8 text-gray-400 text-lg"></i>
                        <input list="productos-list" id="input-busqueda" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none transition-all" placeholder="Escribe nombre o código..." onchange="cargarDatosProducto()">

                        <datalist id="productos-list">
                            <?php foreach ($productos as $p): ?>
                                <option value="<?= $p['nombre'] ?>" data-id="<?= $p['id_producto'] ?>" data-costo="<?= $p['precio_compra'] ?>">
                                    Stock Actual: <?= $p['stock'] ?>
                                </option>
                            <?php endforeach; ?>
                        </datalist>

                        <input type="hidden" id="producto-id-seleccionado">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Costo Unit. (Bs)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400 font-bold">Bs</span>
                            <input type="number" id="input-costo" step="0.01" class="w-full pl-8 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none font-medium">
                        </div>
                    </div>

                    <div class="md:col-span-3 flex gap-2">
                        <div class="w-20">
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Cant.</label>
                            <input type="number" id="input-cantidad" value="1" min="1" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 outline-none font-bold text-center">
                        </div>
                        <button onclick="agregarALista()" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-md transition-all flex items-center justify-center" title="Agregar">
                            <i class="ph ph-plus text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden min-h-[300px] flex flex-col">
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs border-b border-gray-100">
                            <tr>
                                <th class="p-4 pl-6">Producto</th>
                                <th class="p-4 text-center">Cant.</th>
                                <th class="p-4 text-right">Costo</th>
                                <th class="p-4 text-right">Subtotal</th>
                                <th class="p-4 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="tabla-compras-body" class="divide-y divide-gray-50">
                        </tbody>
                    </table>

                    <div id="empty-msg" class="flex flex-col items-center justify-center py-12 text-gray-400">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                            <i class="ph ph-shopping-bag text-3xl text-gray-300"></i>
                        </div>
                        <p>No hay productos en la lista.</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 sticky top-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="ph ph-calculator text-purple-500"></i> Resumen
                </h3>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <span>Total Items</span>
                        <span class="font-medium bg-gray-100 px-2 py-0.5 rounded text-gray-800" id="lbl-items">0</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <span class="text-base font-bold text-gray-700">Total a Pagar</span>
                        <span class="text-3xl font-bold text-purple-600" id="lbl-total">Bs. 0.00</span>
                    </div>
                </div>

                <form id="form-compra" action="index.php?controller=compra&action=guardar" method="POST">
                    <input type="hidden" name="proveedor_id" id="hidden-proveedor">
                    <input type="hidden" name="total_compra" id="hidden-total">
                    <input type="hidden" name="productos_json" id="hidden-json">

                    <button type="button" onclick="procesarCompra()" class="group w-full py-4 bg-gray-900 hover:bg-gray-800 text-white rounded-xl font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 flex justify-center items-center gap-3">
                        <span>Confirmar Entrada</span>
                        <i class="ph-bold ph-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>

                <div class="mt-4 flex items-start gap-2 bg-blue-50 p-3 rounded-lg text-xs text-blue-700 border border-blue-100">
                    <i class="ph-fill ph-info text-lg mt-0.5"></i>
                    <p>Al confirmar, el stock se actualizará automáticamente y se registrará el ingreso en la bitácora.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    let carritoCompra = [];

    // Lógica del Autocomplete Nativo (Datalist)
    // Cuando el usuario selecciona una opción, el input cambia. Detectamos ese cambio.
    function cargarDatosProducto() {
        const input = document.getElementById('input-busqueda');
        const datalist = document.getElementById('productos-list');
        const valor = input.value;

        // Buscar la opción seleccionada en el datalist
        const opcion = Array.from(datalist.options).find(opt => opt.value === valor);

        if (opcion) {
            // Si encontró coincidencia exacta, llenamos los campos ocultos
            document.getElementById('producto-id-seleccionado').value = opcion.getAttribute('data-id');
            document.getElementById('input-costo').value = opcion.getAttribute('data-costo');
            document.getElementById('input-cantidad').focus(); // Mover foco a cantidad
        } else {
            // Si escribió algo que no está en la lista
            document.getElementById('producto-id-seleccionado').value = '';
            document.getElementById('input-costo').value = '';
        }
    }

    function agregarALista() {
        const id = document.getElementById('producto-id-seleccionado').value;
        const nombre = document.getElementById('input-busqueda').value;
        const costo = parseFloat(document.getElementById('input-costo').value);
        const cantidad = parseInt(document.getElementById('input-cantidad').value);

        if (!id || cantidad <= 0 || isNaN(costo)) {
            Swal.fire({
                icon: 'warning',
                title: 'Datos Incompletos',
                text: 'Por favor selecciona un producto válido de la lista.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return;
        }

        // Buscar si ya existe para sumar cantidad
        const existe = carritoCompra.find(i => i.id === id);
        if (existe) {
            existe.cantidad += cantidad;
            existe.subtotal = existe.cantidad * existe.precio;
        } else {
            carritoCompra.push({
                id: id,
                nombre: nombre,
                precio: costo,
                cantidad: cantidad,
                subtotal: costo * cantidad
            });
        }

        renderTabla();

        // Limpiar inputs para el siguiente
        document.getElementById('input-busqueda').value = '';
        document.getElementById('producto-id-seleccionado').value = '';
        document.getElementById('input-costo').value = '';
        document.getElementById('input-cantidad').value = '1';
        document.getElementById('input-busqueda').focus();
    }

    function renderTabla() {
        const tbody = document.getElementById('tabla-compras-body');
        const emptyMsg = document.getElementById('empty-msg');
        const lblTotal = document.getElementById('lbl-total');
        const lblItems = document.getElementById('lbl-items');

        tbody.innerHTML = '';
        let total = 0;
        let items = 0;

        if (carritoCompra.length > 0) {
            emptyMsg.classList.add('hidden');

            carritoCompra.forEach((p, index) => {
                total += p.subtotal;
                items += p.cantidad;
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="p-4 pl-6 font-medium text-gray-800">${p.nombre}</td>
                        <td class="p-4 text-center">
                            <span class="bg-gray-100 px-2 py-1 rounded text-xs font-bold text-gray-600">${p.cantidad}</span>
                        </td>
                        <td class="p-4 text-right text-gray-500">Bs. ${p.precio.toFixed(2)}</td>
                        <td class="p-4 text-right font-bold text-gray-800">Bs. ${p.subtotal.toFixed(2)}</td>
                        <td class="p-4 text-right">
                            <button onclick="eliminarItem(${index})" class="text-gray-300 hover:text-red-500 transition-colors p-1 rounded hover:bg-red-50"><i class="ph-bold ph-trash text-lg"></i></button>
                        </td>
                    </tr>
                `;
            });
        } else {
            emptyMsg.classList.remove('hidden');
        }

        lblTotal.innerText = 'Bs. ' + total.toFixed(2);
        lblItems.innerText = items;
    }

    function eliminarItem(index) {
        carritoCompra.splice(index, 1);
        renderTabla();
    }

    function procesarCompra() {
        if (carritoCompra.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Lista Vacía',
                text: 'Agrega al menos un producto.'
            });
            return;
        }

        Swal.fire({
            title: '¿Confirmar Ingreso?',
            text: "Se actualizará el stock de " + carritoCompra.length + " productos.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed', // Purple
            cancelButtonColor: '#d1d5db',
            confirmButtonText: 'Sí, registrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Llenar datos ocultos
                document.getElementById('hidden-proveedor').value = document.getElementById('select-proveedor').value;
                document.getElementById('hidden-total').value = carritoCompra.reduce((sum, i) => sum + i.subtotal, 0);
                document.getElementById('hidden-json').value = JSON.stringify(carritoCompra);

                // Spinner
                Swal.fire({
                    title: 'Guardando...',
                    text: 'Actualizando inventario',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                document.getElementById('form-compra').submit();
            }
        });
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>