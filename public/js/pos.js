// public/js/pos.js - Punto de venta

let carrito = [];
let filtroActual = 'all';

const DIAS_ALERTA_VENCIMIENTO = 30;

const esc = (texto) => String(texto ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const bs = (monto) => 'Bs. ' + Number(monto).toFixed(2);

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2200,
    timerProgressBar: true
});

// ---------- Productos ----------

function diasParaVencer(p) {
    if (!p.fecha_vencimiento) return null;
    const hoy = new Date(); hoy.setHours(0, 0, 0, 0);
    const vence = new Date(p.fecha_vencimiento + 'T00:00:00');
    return Math.round((vence - hoy) / 86400000);
}

function productosVisibles() {
    const texto = document.getElementById('buscador').value.trim().toLowerCase();
    return productosDB.filter(p => {
        if (filtroActual === 'MEDICAMENTO' || filtroActual === 'GENERAL') {
            if (p.tipo !== filtroActual) return false;
        } else if (filtroActual === 'STOCK' && parseInt(p.stock) <= 0) {
            return false;
        }
        if (!texto) return true;
        return p.nombre.toLowerCase().includes(texto) || (p.codigo_barras && p.codigo_barras.toLowerCase().includes(texto));
    });
}

function tarjetaProducto(p) {
    const stock = parseInt(p.stock);
    const minimo = parseInt(p.stock_minimo ?? 5);
    const sinStock = stock <= 0;
    const enCarrito = carrito.find(i => i.id == p.id_producto);
    const dias = diasParaVencer(p);

    let badgeStock;
    if (sinStock) {
        badgeStock = '<span class="bg-red-100 text-red-700"><i class="ph ph-prohibit"></i> Agotado</span>';
    } else if (stock <= minimo) {
        badgeStock = `<span class="bg-amber-100 text-amber-700"><i class="ph ph-warning"></i> ${stock} und.</span>`;
    } else {
        badgeStock = `<span class="bg-emerald-100 text-emerald-700"><i class="ph ph-stack"></i> ${stock} und.</span>`;
    }

    let badgeVence = '';
    if (dias !== null && dias < 0) {
        badgeVence = '<span class="absolute bottom-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-600 text-white flex items-center gap-1"><i class="ph ph-calendar-x"></i> Vencido</span>';
    } else if (dias !== null && dias <= DIAS_ALERTA_VENCIMIENTO) {
        badgeVence = `<span class="absolute bottom-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 flex items-center gap-1"><i class="ph ph-hourglass-medium"></i> Vence en ${dias} d</span>`;
    }

    const clases = sinStock
        ? 'opacity-60 cursor-not-allowed'
        : 'cursor-pointer hover:border-sky-300 hover:shadow-md hover:-translate-y-0.5';
    const borde = enCarrito ? 'border-sky-400 ring-2 ring-sky-100' : 'border-gray-200';

    return `
        <div class="bg-white rounded-xl border ${borde} shadow-sm transition-all group overflow-hidden relative ${clases}"
             ${sinStock ? '' : `onclick="agregarCarrito(${p.id_producto})"`}>
            <div class="h-28 w-full bg-gray-50 relative">
                ${imagenProductoHtml(p, 'w-full h-full object-contain p-2', 'text-5xl')}
                <div class="absolute top-2 left-2 [&>span]:text-[10px] [&>span]:font-bold [&>span]:px-2 [&>span]:py-0.5 [&>span]:rounded-full [&>span]:flex [&>span]:items-center [&>span]:gap-1">
                    ${badgeStock}
                </div>
                ${enCarrito ? `<span class="absolute top-2 right-2 h-6 min-w-[1.5rem] px-1.5 rounded-full bg-sky-600 text-white text-xs font-bold flex items-center justify-center shadow">${enCarrito.cantidad}</span>` : ''}
                ${badgeVence}
            </div>
            <div class="p-3">
                <h3 class="text-sm font-semibold text-gray-800 line-clamp-2 leading-snug min-h-[2.5rem]" title="${esc(p.nombre)}">${esc(p.nombre)}</h3>
                <p class="text-[11px] text-gray-400 mt-1 flex items-center gap-1 truncate"><i class="ph ph-barcode"></i> ${esc(p.codigo_barras || 'Sin código')}</p>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-base font-bold text-sky-700">${bs(p.precio_venta)}</span>
                    <span class="h-8 w-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center group-hover:bg-sky-600 group-hover:text-white transition-colors">
                        <i class="ph-bold ph-plus"></i>
                    </span>
                </div>
            </div>
        </div>`;
}

function renderProductos() {
    const lista = productosVisibles();
    const contenedor = document.getElementById('grid-productos');
    document.getElementById('contador-productos').innerText = lista.length;

    if (lista.length === 0) {
        contenedor.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center py-20 text-gray-400">
                <i class="ph-duotone ph-magnifying-glass text-6xl text-sky-200 mb-3"></i>
                <p class="font-medium">No se encontraron productos</p>
                <p class="text-sm">Prueba con otro nombre, código o filtro.</p>
            </div>`;
        return;
    }
    contenedor.innerHTML = lista.map(tarjetaProducto).join('');
}

function aplicarFiltro(filtro) {
    filtroActual = filtro;
    document.querySelectorAll('.filtro-btn').forEach(btn => {
        const activo = btn.dataset.filtro === filtro;
        btn.classList.toggle('bg-sky-600', activo);
        btn.classList.toggle('text-white', activo);
        btn.classList.toggle('border-sky-600', activo);
        btn.classList.toggle('bg-white', !activo);
        btn.classList.toggle('text-gray-600', !activo);
        btn.classList.toggle('border-gray-200', !activo);
        btn.classList.toggle('hover:bg-gray-50', !activo);
    });
    renderProductos();
}

// ---------- Carrito ----------

function agregarCarrito(id) {
    const producto = productosDB.find(p => p.id_producto == id);
    if (!producto) return;
    const stock = parseInt(producto.stock);
    const existe = carrito.find(item => item.id == id);

    if (stock <= 0) {
        Toast.fire({ icon: 'error', title: 'Producto agotado' });
        return;
    }
    if (existe) {
        if (existe.cantidad >= stock) {
            Toast.fire({ icon: 'warning', title: `Solo hay ${stock} unidades disponibles` });
            return;
        }
        existe.cantidad++;
    } else {
        carrito.push({
            id: producto.id_producto,
            nombre: producto.nombre,
            precio: parseFloat(producto.precio_venta),
            cantidad: 1,
            max_stock: stock,
            imagen: producto.imagen,
            tipo: producto.tipo
        });
    }
    actualizarTodo();
}

function cambiarCantidad(index, delta) {
    const item = carrito[index];
    const nueva = item.cantidad + delta;
    if (nueva <= 0) {
        eliminarItem(index);
        return;
    }
    if (nueva > item.max_stock) {
        Toast.fire({ icon: 'warning', title: `Stock máximo: ${item.max_stock} unidades` });
        return;
    }
    item.cantidad = nueva;
    actualizarTodo();
}

function fijarCantidad(index, valor) {
    const item = carrito[index];
    let cant = parseInt(valor);
    if (isNaN(cant) || cant < 1) cant = 1;
    if (cant > item.max_stock) {
        cant = item.max_stock;
        Toast.fire({ icon: 'warning', title: `Stock máximo: ${item.max_stock} unidades` });
    }
    item.cantidad = cant;
    actualizarTodo();
}

function eliminarItem(index) {
    carrito.splice(index, 1);
    actualizarTodo();
}

function vaciarCarrito() {
    if (carrito.length === 0) return;
    Swal.fire({
        title: '¿Vaciar la venta?',
        text: 'Se quitarán todos los productos del carrito.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626'
    }).then(r => {
        if (r.isConfirmed) {
            carrito = [];
            actualizarTodo();
        }
    });
}

function totalCarrito() {
    return carrito.reduce((suma, item) => suma + item.precio * item.cantidad, 0);
}

function renderCarrito() {
    const contenedor = document.getElementById('cart-body');
    const total = totalCarrito();
    const articulos = carrito.reduce((suma, item) => suma + item.cantidad, 0);

    if (carrito.length === 0) {
        contenedor.innerHTML = `
            <div class="h-full flex flex-col items-center justify-center text-center p-8 text-gray-400">
                <div class="h-20 w-20 rounded-full bg-sky-50 flex items-center justify-center mb-4">
                    <i class="ph-duotone ph-shopping-cart-simple text-5xl text-sky-300"></i>
                </div>
                <p class="font-semibold text-gray-500">El carrito está vacío</p>
                <p class="text-sm mt-1">Haz clic en un producto o escanea su código de barras.</p>
            </div>`;
    } else {
        contenedor.innerHTML = carrito.map((item, index) => `
            <div class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors">
                <div class="h-12 w-12 rounded-lg bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                    ${imagenProductoHtml(item, 'w-full h-full object-contain p-1', 'text-2xl')}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate" title="${esc(item.nombre)}">${esc(item.nombre)}</p>
                    <p class="text-xs text-gray-400">${bs(item.precio)} c/u</p>
                    <div class="flex items-center gap-1 mt-1.5">
                        <button onclick="cambiarCantidad(${index}, -1)" class="h-7 w-7 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 flex items-center justify-center" title="Quitar uno">
                            <i class="ph-bold ${item.cantidad === 1 ? 'ph-trash text-red-500' : 'ph-minus'} text-xs"></i>
                        </button>
                        <input type="number" min="1" max="${item.max_stock}" value="${item.cantidad}" onchange="fijarCantidad(${index}, this.value)"
                            class="w-12 h-7 text-center text-sm font-bold border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none">
                        <button onclick="cambiarCantidad(${index}, 1)" class="h-7 w-7 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 flex items-center justify-center disabled:opacity-40" title="Agregar uno" ${item.cantidad >= item.max_stock ? 'disabled' : ''}>
                            <i class="ph-bold ph-plus text-xs"></i>
                        </button>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2 shrink-0">
                    <button onclick="eliminarItem(${index})" class="text-gray-300 hover:text-red-500 transition" title="Quitar producto">
                        <i class="ph ph-x-circle text-xl"></i>
                    </button>
                    <span class="text-sm font-bold text-gray-800">${bs(item.precio * item.cantidad)}</span>
                </div>
            </div>`).join('');
    }

    document.getElementById('lbl-articulos').innerText = articulos;
    document.getElementById('lbl-subtotal').innerText = bs(total);
    document.getElementById('lbl-total').innerText = bs(total);
    document.getElementById('btn-cobrar').disabled = carrito.length === 0;
    document.getElementById('btn-vaciar').disabled = carrito.length === 0;
}

function actualizarTodo() {
    renderCarrito();
    renderProductos();
}

// ---------- Cobro ----------

function abrirCobro() {
    if (carrito.length === 0) {
        Toast.fire({ icon: 'info', title: 'Agrega productos antes de cobrar' });
        return;
    }
    const total = totalCarrito();
    const montosRapidos = [10, 20, 50, 100, 200].filter(m => m >= total);

    Swal.fire({
        title: '<span class="flex items-center justify-center gap-2 text-gray-800"><i class="ph-duotone ph-cash-register text-sky-600"></i> Cobrar venta</span>',
        width: 440,
        showCancelButton: true,
        confirmButtonText: '<span class="flex items-center gap-2"><i class="ph-bold ph-check-circle"></i> Confirmar pago</span>',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#059669',
        cancelButtonColor: '#94a3b8',
        focusConfirm: false,
        html: `
            <div class="text-left space-y-4">
                <div class="rounded-xl bg-sky-50 border border-sky-100 p-4 text-center">
                    <p class="text-xs font-bold text-sky-700 uppercase tracking-wider">Total a pagar</p>
                    <p class="text-4xl font-extrabold text-sky-900 mt-1">${bs(total)}</p>
                    <p class="text-xs text-sky-700/70 mt-1">${carrito.reduce((s, i) => s + i.cantidad, 0)} artículos</p>
                </div>

                <div>
                    <label class="text-xs text-gray-500 font-bold uppercase block mb-1.5">Monto recibido</label>
                    <div class="relative">
                        <i class="ph ph-money absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
                        <input id="cobro-recibido" type="number" step="0.01" min="0" placeholder="0.00"
                            class="w-full pl-11 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 outline-none font-bold text-gray-800 text-xl">
                    </div>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <button type="button" data-monto="${total.toFixed(2)}" class="monto-rapido px-3 py-1.5 text-xs font-bold rounded-lg bg-sky-600 text-white hover:bg-sky-700">Exacto</button>
                        ${montosRapidos.map(m => `<button type="button" data-monto="${m}" class="monto-rapido px-3 py-1.5 text-xs font-bold rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100">Bs. ${m}</button>`).join('')}
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3">
                    <span class="text-sm font-bold text-gray-600 flex items-center gap-1.5"><i class="ph ph-hand-coins text-lg"></i> Cambio</span>
                    <span id="cobro-cambio" class="text-xl font-bold text-gray-400">Bs. 0.00</span>
                </div>

                <label class="flex items-center gap-3 cursor-pointer rounded-xl border border-gray-200 px-4 py-3 hover:bg-gray-50">
                    <input type="checkbox" id="cobro-factura" class="w-4 h-4 accent-sky-600" checked>
                    <i class="ph ph-printer text-xl text-gray-500"></i>
                    <span class="text-sm text-gray-700 font-medium">Imprimir factura al terminar</span>
                </label>
            </div>`,
        didOpen: () => {
            const input = document.getElementById('cobro-recibido');
            const actualizar = () => {
                const cambio = (parseFloat(input.value) || 0) - total;
                const etiqueta = document.getElementById('cobro-cambio');
                if (input.value === '') {
                    etiqueta.innerText = 'Bs. 0.00';
                    etiqueta.className = 'text-xl font-bold text-gray-400';
                } else if (cambio >= 0) {
                    etiqueta.innerText = bs(cambio);
                    etiqueta.className = 'text-xl font-bold text-emerald-600';
                } else {
                    etiqueta.innerText = 'Faltan ' + bs(Math.abs(cambio));
                    etiqueta.className = 'text-xl font-bold text-red-500';
                }
            };
            input.addEventListener('input', actualizar);
            document.querySelectorAll('.monto-rapido').forEach(btn => btn.addEventListener('click', () => {
                input.value = btn.dataset.monto;
                actualizar();
                input.focus();
            }));
            setTimeout(() => input.focus(), 100);
        },
        preConfirm: () => {
            const recibido = parseFloat(document.getElementById('cobro-recibido').value) || 0;
            if (recibido + 0.001 < total) {
                Swal.showValidationMessage(`Monto insuficiente. Faltan ${bs(total - recibido)}`);
                return false;
            }
            return { imprimir: document.getElementById('cobro-factura').checked };
        }
    }).then(r => {
        if (r.isConfirmed) enviarVenta(r.value.imprimir);
    });
}

function enviarVenta(imprimir) {
    document.getElementById('input-cliente').value = document.getElementById('select-cliente').value;
    document.getElementById('input-json').value = JSON.stringify(carrito.map(i => ({ id: i.id, cantidad: i.cantidad })));
    sessionStorage.setItem('pos_imprimir', imprimir ? '1' : '0');

    Swal.fire({
        title: 'Procesando venta',
        html: 'Guardando el registro y actualizando el stock...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    document.getElementById('form-venta').submit();
}

// ---------- Resultado de la venta ----------

function mostrarResultado() {
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status');
    const idVenta = params.get('id_venta');
    const limpiarUrl = () => window.history.replaceState({}, document.title, window.location.pathname + '?controller=venta&action=crear');

    if (status === 'success' && idVenta) {
        const urlTicket = `index.php?controller=reporte&action=ticket&id=${encodeURIComponent(idVenta)}`;
        const imprimir = sessionStorage.getItem('pos_imprimir') !== '0';
        sessionStorage.removeItem('pos_imprimir');

        Swal.fire({
            icon: 'success',
            title: 'Venta registrada',
            html: `<p class="text-gray-600">Venta <b>#${String(idVenta).padStart(6, '0')}</b> guardada correctamente.</p>`,
            showCancelButton: true,
            confirmButtonText: '<span class="flex items-center gap-2"><i class="ph-bold ph-printer"></i> Imprimir factura</span>',
            cancelButtonText: '<span class="flex items-center gap-2"><i class="ph-bold ph-plus"></i> Nueva venta</span>',
            confirmButtonColor: '#0284c7',
            cancelButtonColor: '#059669',
            focusCancel: !imprimir
        }).then(r => {
            if (r.isConfirmed) window.open(urlTicket, '_blank');
            limpiarUrl();
        });
    } else if (status === 'error') {
        Swal.fire({
            icon: 'error',
            title: 'No se registró la venta',
            text: params.get('msg') || 'Ocurrió un error al registrar la venta.',
            confirmButtonColor: '#0284c7'
        }).then(limpiarUrl);
    }
}

// ---------- Inicio ----------

document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.getElementById('buscador');

    buscador.addEventListener('input', renderProductos);

    // Enter: si el texto coincide con un código de barras (lector), se agrega directo
    buscador.addEventListener('keydown', e => {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const texto = buscador.value.trim().toLowerCase();
        if (!texto) return;
        const porCodigo = productosDB.find(p => p.codigo_barras && p.codigo_barras.toLowerCase() === texto);
        const visibles = productosVisibles();
        const producto = porCodigo || (visibles.length === 1 ? visibles[0] : null);
        if (producto) {
            agregarCarrito(producto.id_producto);
            buscador.value = '';
            renderProductos();
        } else if (visibles.length === 0) {
            Toast.fire({ icon: 'error', title: 'Producto no encontrado' });
        }
    });

    document.querySelectorAll('.filtro-btn').forEach(btn => btn.addEventListener('click', () => aplicarFiltro(btn.dataset.filtro)));

    document.addEventListener('keydown', e => {
        if (Swal.isVisible()) return;
        if (e.key === 'F2') {
            e.preventDefault();
            buscador.focus();
            buscador.select();
        } else if (e.key === 'F9') {
            e.preventDefault();
            abrirCobro();
        }
    });

    aplicarFiltro('all');
    renderCarrito();
    buscador.focus();
    mostrarResultado();
});
