// public/js/pos.js

let carrito = [];
let productosFiltrados = [...productosDB];

// 1. Renderizar Productos en el Grid
function renderProductos() {
    const contenedor = document.getElementById('grid-productos');
    contenedor.innerHTML = '';

    productosFiltrados.forEach(p => {
        // Validar stock
        const sinStock = p.stock <= 0;
        const opacidad = sinStock ? 'opacity-50 grayscale' : '';
        const cursor = sinStock ? 'cursor-not-allowed' : 'cursor-pointer';
        const clickEvent = sinStock ? '' : `onclick="agregarCarrito(${p.id_producto})"`;

        const html = `
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all group overflow-hidden ${opacidad} ${cursor}" ${clickEvent}>
                <div class="h-32 w-full bg-gray-100 relative">
                    ${imagenProductoHtml(p, 'w-full h-full object-contain p-2', 'text-5xl')}
                    ${sinStock ? '<div class="absolute inset-0 bg-black/10 flex items-center justify-center font-bold text-red-600 bg-white/80">AGOTADO</div>' : ''}
                    <div class="absolute top-2 right-2 bg-white/90 px-2 py-1 rounded text-xs font-bold shadow-sm">
                        Stock: ${p.stock}
                    </div>
                </div>
                <div class="p-3">
                    <h3 class="text-sm font-bold text-gray-800 line-clamp-1" title="${p.nombre}">${p.nombre}</h3>
                    <p class="text-xs text-gray-500 mb-2">${p.codigo_barras || 'S/C'}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-brand-600">Bs. ${parseFloat(p.precio_venta).toFixed(2)}</span>
                        <div class="h-8 w-8 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition-colors">
                            <i class="ph ph-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
        `;
        contenedor.innerHTML += html;
    });
}

// 2. Agregar al Carrito
function agregarCarrito(id) {
    const producto = productosDB.find(p => p.id_producto == id);
    
    // Verificar si ya está en el carrito
    const existe = carrito.find(item => item.id == id);
    
    if (existe) {
        if (existe.cantidad < producto.stock) {
            existe.cantidad++;
            existe.subtotal = existe.cantidad * parseFloat(producto.precio_venta);
        } else {
            alert("No hay más stock disponible de este producto.");
        }
    } else {
        if (producto.stock > 0) {
            carrito.push({
                id: producto.id_producto,
                nombre: producto.nombre,
                precio: parseFloat(producto.precio_venta),
                cantidad: 1,
                subtotal: parseFloat(producto.precio_venta),
                max_stock: producto.stock
            });
        }
    }
    renderCarrito();
}

// 3. Renderizar Carrito
function renderCarrito() {
    const tbody = document.getElementById('cart-body');
    const lblTotal = document.getElementById('lbl-total');
    const lblSubtotal = document.getElementById('lbl-subtotal');
    
    tbody.innerHTML = '';
    let total = 0;

    if (carrito.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-8 text-gray-400"><i class="ph ph-shopping-cart text-4xl mb-2"></i><p>Carrito vacío</p></td></tr>`;
    } else {
        carrito.forEach((item, index) => {
            total += item.subtotal;
            tbody.innerHTML += `
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-2 py-2">
                        <div class="font-medium text-gray-800 text-xs truncate max-w-[100px]">${item.nombre}</div>
                        <div class="text-[10px] text-gray-500">Bs. ${item.precio.toFixed(2)}</div>
                    </td>
                    <td class="px-2 py-2 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="cambiarCantidad(${index}, -1)" class="w-5 h-5 bg-gray-200 rounded text-gray-600 hover:bg-gray-300 flex items-center justify-center text-xs">-</button>
                            <span class="text-xs font-bold w-4">${item.cantidad}</span>
                            <button onclick="cambiarCantidad(${index}, 1)" class="w-5 h-5 bg-gray-200 rounded text-gray-600 hover:bg-gray-300 flex items-center justify-center text-xs">+</button>
                        </div>
                    </td>
                    <td class="px-2 py-2 text-right font-bold text-xs text-gray-700">
                        ${item.subtotal.toFixed(2)}
                    </td>
                    <td class="px-2 py-2 text-center">
                        <button onclick="eliminarItem(${index})" class="text-red-400 hover:text-red-600">
                            <i class="ph ph-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    lblSubtotal.innerText = 'Bs. ' + total.toFixed(2);
    lblTotal.innerText = 'Bs. ' + total.toFixed(2);
}

// 4. Cambiar Cantidad
function cambiarCantidad(index, delta) {
    const item = carrito[index];
    const nuevaCant = item.cantidad + delta;

    if (nuevaCant > 0 && nuevaCant <= item.max_stock) {
        item.cantidad = nuevaCant;
        item.subtotal = item.cantidad * item.precio;
        renderCarrito();
    } else if (nuevaCant > item.max_stock) {
        alert("Stock máximo alcanzado.");
    }
}

// 5. Eliminar Item
function eliminarItem(index) {
    carrito.splice(index, 1);
    renderCarrito();
}

// 6. Filtrar Productos (Buscador)
function filtrarProductos() {
    const texto = document.getElementById('buscador').value.toLowerCase();
    productosFiltrados = productosDB.filter(p => 
        p.nombre.toLowerCase().includes(texto) || 
        (p.codigo_barras && p.codigo_barras.includes(texto))
    );
    renderProductos();
}

function filtrarCategoria(tipo) {
    if(tipo === 'all') {
        productosFiltrados = [...productosDB];
    } else {
        productosFiltrados = productosDB.filter(p => p.tipo === tipo);
    }
    renderProductos();
}

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    renderProductos();
});