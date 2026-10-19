<?php ob_start(); ?>

<div class="max-w-7xl mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div class="w-full md:w-auto">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="ph ph-package text-brand-500"></i> Inventario
            </h1>
            <p class="text-gray-500 text-sm mt-1">Gestión visual de productos y existencias.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            
            <div class="relative group w-full sm:w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-3 text-gray-400 group-focus-within:text-brand-500 transition-colors"></i>
                <input type="text" id="buscador" onkeyup="filtrarProductos()" placeholder="Buscar producto..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none shadow-sm transition-all">
            </div>

            <?php if(isAdmin()): ?>
            <a href="index.php?controller=producto&action=crear" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 whitespace-nowrap">
                <i class="ph ph-plus-circle text-lg"></i>
                <span>Nuevo Producto</span>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex gap-3 overflow-x-auto pb-4 mb-2 no-scrollbar">
        <button onclick="filtrarCategoria('todos')" class="filter-btn active px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 whitespace-nowrap transition-colors ring-2 ring-transparent ring-offset-1">
            Todos
        </button>
        <button onclick="filtrarCategoria('MEDICAMENTO')" class="filter-btn px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 whitespace-nowrap transition-colors">
            Medicamentos
        </button>
        <button onclick="filtrarCategoria('GENERAL')" class="filter-btn px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 whitespace-nowrap transition-colors">
            Insumos
        </button>
        <button onclick="filtrarStockBajo()" class="filter-btn px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 whitespace-nowrap transition-colors">
            Bajo Stock
        </button>
    </div>

    <?php if(empty($productos)): ?>
        <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-300">
            <i class="ph ph-ghost text-6xl text-gray-200 mb-4"></i>
            <p class="text-gray-500 font-medium">No hay productos registrados aún.</p>
            <?php if(isAdmin()): ?>
                <a href="index.php?controller=producto&action=crear" class="text-brand-600 hover:underline text-sm mt-2 block">Crear el primero</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div id="grid-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8 min-h-[400px]">
            </div>

        <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-xl border border-gray-100 shadow-sm gap-4">
            <span class="text-sm text-gray-500">
                Mostrando <span id="lbl-inicio" class="font-bold text-gray-800">0</span> - <span id="lbl-fin" class="font-bold text-gray-800">0</span> de <span id="lbl-total" class="font-bold text-gray-800">0</span>
            </span>
            
            <div class="flex items-center gap-2">
                <button onclick="cambiarPagina('prev')" id="btn-prev" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <i class="ph ph-caret-left text-lg"></i>
                </button>
                
                <div id="pagination-numbers" class="flex gap-1 overflow-x-auto max-w-[250px] no-scrollbar"></div>

                <button onclick="cambiarPagina('next')" id="btn-next" class="w-9 h-9 flex items-center justify-center border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <i class="ph ph-caret-right text-lg"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

</div>

<script src="js/producto-imagen.js"></script>
<script>
    // 1. Datos iniciales desde PHP
    const productosDB = <?= json_encode($productos) ?>;
    const esAdmin = <?= isAdmin() ? 'true' : 'false' ?>; // Tu función PHP pasa a JS
    
    let productosFiltrados = [...productosDB]; // Copia para filtrar sin perder datos

    // CONFIGURACIÓN 4x4
    const ITEMS_POR_PAGINA = 16; 
    let paginaActual = 1;

    // 2. Renderizar Grid
    function renderizarGrid() {
        const contenedor = document.getElementById('grid-container');
        if(!contenedor) return;
        
        contenedor.innerHTML = '';

        // Cálculos de paginación
        const totalItems = productosFiltrados.length;
        const totalPaginas = Math.ceil(totalItems / ITEMS_POR_PAGINA);
        
        // Ajustar página actual si nos pasamos (ej: al filtrar)
        if(paginaActual > totalPaginas) paginaActual = 1;
        if(paginaActual < 1) paginaActual = 1;

        const inicio = (paginaActual - 1) * ITEMS_POR_PAGINA;
        const fin = inicio + ITEMS_POR_PAGINA;
        const productosPagina = productosFiltrados.slice(inicio, fin);

        // Actualizar Info Paginación
        document.getElementById('lbl-inicio').innerText = totalItems > 0 ? inicio + 1 : 0;
        document.getElementById('lbl-fin').innerText = Math.min(fin, totalItems);
        document.getElementById('lbl-total').innerText = totalItems;

        // Si no hay resultados
        if(productosPagina.length === 0) {
            contenedor.innerHTML = `<div class="col-span-full text-center py-20 text-gray-400 flex flex-col items-center"><i class="ph ph-magnifying-glass text-4xl mb-2"></i><p>No se encontraron coincidencias.</p></div>`;
            renderizarPaginacion(0);
            return;
        }

        // Generar Tarjetas
        productosPagina.forEach(p => {
            const stockBajo = p.stock <= 5;
            const esMedicamento = p.tipo === 'MEDICAMENTO';

            // HTML de la Tarjeta
            const html = `
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group flex flex-col h-full relative">
                    <div class="h-48 bg-gray-50 relative p-4 flex items-center justify-center">
                        ${imagenProductoHtml(p, 'h-full w-full object-contain group-hover:scale-105 transition-transform duration-500', 'text-7xl')}
                        
                        <div class="absolute top-3 left-3 flex flex-col gap-1 items-start">
                            ${esMedicamento ? '<span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-full border border-blue-200">MED</span>' : ''}
                            ${stockBajo ? '<span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded-full border border-red-200 animate-pulse flex items-center gap-1"><i class="ph ph-warning"></i> BAJO</span>' : ''}
                        </div>

                        ${esAdmin ? `
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300 backdrop-blur-[1px]">
                            <a href="index.php?controller=producto&action=editar&id=${p.id_producto}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-700 hover:text-brand-600 hover:scale-110 transition-all shadow-lg" title="Editar">
                                <i class="ph ph-pencil-simple text-xl"></i>
                            </a>
                            <a href="index.php?controller=producto&action=eliminar&id=${p.id_producto}" onclick="return confirm('¿Estás seguro de eliminar este producto?')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-red-500 hover:text-red-700 hover:scale-110 transition-all shadow-lg" title="Eliminar">
                                <i class="ph ph-trash text-xl"></i>
                            </a>
                        </div>` : ''}
                    </div>

                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-1">${p.categoria || 'GENERAL'}</p>
                            <h3 class="font-bold text-gray-800 text-sm line-clamp-2 leading-tight min-h-[2.5em]" title="${p.nombre}">${p.nombre}</h3>
                            <p class="text-xs text-gray-400 mt-1">Cod: ${p.codigo_barras || 'S/C'}</p>
                        </div>
                        <div class="flex items-end justify-between mt-3 pt-3 border-t border-gray-50">
                            <span class="text-lg font-bold text-brand-600">Bs. ${parseFloat(p.precio_venta).toFixed(2)}</span>
                            <span class="text-xs font-medium px-2 py-1 rounded bg-gray-100 text-gray-600">Stock: ${p.stock}</span>
                        </div>
                    </div>
                </div>
            `;
            contenedor.innerHTML += html;
        });

        renderizarPaginacion(totalPaginas);
    }

    // 3. Renderizar Botones de Paginación
    function renderizarPaginacion(totalPaginas) {
        const contenedorNums = document.getElementById('pagination-numbers');
        contenedorNums.innerHTML = '';

        document.getElementById('btn-prev').disabled = paginaActual === 1;
        document.getElementById('btn-next').disabled = paginaActual >= totalPaginas || totalPaginas === 0;

        for (let i = 1; i <= totalPaginas; i++) {
            // Lógica simple para mostrar botones: si son muchas páginas, mostrar todas (scroll horizontal activado en CSS)
            const btn = document.createElement('button');
            btn.innerText = i;
            btn.onclick = () => { paginaActual = i; renderizarGrid(); };
            
            // Estilo botón activo vs inactivo
            if (i === paginaActual) {
                btn.className = 'min-w-[36px] h-9 rounded-lg bg-brand-600 text-white font-bold text-sm shadow-md transition-all';
            } else {
                btn.className = 'min-w-[36px] h-9 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-sm transition-all';
            }
            
            contenedorNums.appendChild(btn);
        }
    }

    // 4. Funciones de Control
    function cambiarPagina(accion) {
        const totalItems = productosFiltrados.length;
        const totalPaginas = Math.ceil(totalItems / ITEMS_POR_PAGINA);

        if(accion === 'prev' && paginaActual > 1) paginaActual--;
        if(accion === 'next' && paginaActual < totalPaginas) paginaActual++;
        
        renderizarGrid();
    }

    function filtrarProductos() {
        const texto = document.getElementById('buscador').value.toLowerCase();
        // Filtramos sobre la base de datos completa
        productosFiltrados = productosDB.filter(p => 
            p.nombre.toLowerCase().includes(texto) || 
            (p.codigo_barras && p.codigo_barras.toLowerCase().includes(texto))
        );
        paginaActual = 1; // Volver a la página 1 al buscar
        renderizarGrid();
    }

    function filtrarCategoria(tipo) {
        // Estilos botones (Visual)
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active', 'bg-brand-50', 'text-brand-700', 'ring-2', 'ring-brand-200', 'border-brand-200');
            b.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
        });
        
        // Activar el botón presionado
        event.target.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
        event.target.classList.add('active', 'bg-brand-50', 'text-brand-700', 'ring-2', 'ring-brand-200', 'border-brand-200');

        // Lógica de filtro
        if(tipo === 'todos') {
            productosFiltrados = [...productosDB];
        } else {
            productosFiltrados = productosDB.filter(p => p.tipo === tipo);
        }
        
        // Si hay texto en el buscador, reaplicamos ese filtro también
        const texto = document.getElementById('buscador').value.toLowerCase();
        if(texto) {
            productosFiltrados = productosFiltrados.filter(p => p.nombre.toLowerCase().includes(texto));
        }

        paginaActual = 1;
        renderizarGrid();
    }

    function filtrarStockBajo() {
        // Estilos botones
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active', 'bg-brand-50', 'text-brand-700', 'ring-2', 'ring-brand-200');
            b.classList.add('bg-white', 'text-gray-600');
        });
        event.target.classList.remove('bg-white', 'text-gray-600');
        event.target.classList.add('active', 'bg-red-50', 'text-red-700', 'ring-2', 'ring-red-200', 'border-red-200');

        productosFiltrados = productosDB.filter(p => p.stock <= 5);
        paginaActual = 1;
        renderizarGrid();
    }

    // Inicializar al cargar
    document.addEventListener('DOMContentLoaded', () => {
        renderizarGrid();
        
        // Marcar botón "Todos" como activo
        const btnTodos = document.querySelector('.filter-btn'); 
        if(btnTodos) {
            btnTodos.classList.remove('bg-white', 'text-gray-600');
            btnTodos.classList.add('bg-brand-50', 'text-brand-700', 'ring-2', 'ring-brand-200');
        }
    });
</script>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php'; 
?>