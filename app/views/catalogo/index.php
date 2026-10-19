<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo - FarmaPlus</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            900: '#0c4a6e'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Fondo con patrón sutil */
        body {
            background-color: #f8fafc;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="text-gray-800 antialiased">

    <nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-sky-100 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">

                <a href="index.php" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-emerald-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        <i class="ph-fill ph-first-aid"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-gray-800">Farma<span class="text-emerald-500">Plus</span></span>
                </a>

                <button onclick="toggleCarrito()" class="relative group p-2 rounded-xl hover:bg-gray-50 transition-colors">
                    <div class="w-10 h-10 flex items-center justify-center text-gray-600 group-hover:text-brand-600 transition-colors">
                        <i class="ph ph-shopping-cart text-2xl"></i>
                    </div>
                    <span id="cart-count" class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold h-5 w-5 rounded-full flex items-center justify-center shadow-sm border-2 border-white transform scale-0 transition-transform duration-300">0</span>
                </button>
            </div>
        </div>
    </nav>

    <header class="pt-32 pb-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="relative rounded-3xl bg-brand-900 overflow-hidden shadow-2xl p-8 md:p-12 lg:p-16 flex flex-col md:flex-row items-center justify-between gap-8">

            <div class="absolute inset-0 opacity-10">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                </svg>
            </div>

            <div class="relative z-10 max-w-xl text-center md:text-left">
                <span class="inline-block py-1 px-3 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-4 border border-emerald-500/30">Catálogo Digital</span>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-6 leading-tight">
                    Tu salud al alcance <br /> de un clic.
                </h1>
                <p class="text-brand-100 text-lg mb-8 max-w-md mx-auto md:mx-0">
                    Explora nuestro inventario, crea tu lista y retira en farmacia sin esperas.
                </p>

                <div class="relative max-w-md mx-auto md:mx-0 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass text-gray-400 text-xl group-focus-within:text-brand-500 transition-colors"></i>
                    </div>
                    <input type="text" id="buscador" onkeyup="filtrarProductos()"
                        class="block w-full pl-11 pr-4 py-4 bg-white/95 backdrop-blur rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-4 focus:ring-brand-500/30 shadow-lg transition-all"
                        placeholder="Buscar medicamentos, vitaminas...">
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">

        <div class="flex gap-3 mb-8 overflow-x-auto pb-2 no-scrollbar justify-center md:justify-start">
            <button onclick="filtrarCategoria('all')" class="px-5 py-2 rounded-full bg-brand-600 text-white font-medium text-sm shadow-md hover:bg-brand-700 transition-all">Lista Productos</button>
        </div>

        <div id="grid-productos" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($productos as $p): ?>
                <div class="producto-card group bg-white rounded-3xl p-4 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 flex flex-col h-full relative overflow-hidden"
                    data-nombre="<?= strtolower($p['nombre']) ?>"
                    data-categoria="<?= strtolower($p['categoria']) ?>">

                    <div class="absolute top-4 left-4 z-10">
                        <?php if ($p['tipo'] == 'MEDICAMENTO'): ?>
                            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-2.5 py-1 rounded-full border border-blue-100">MEDICINA</span>
                        <?php else: ?>
                            <span class="bg-gray-50 text-gray-500 text-[10px] font-bold px-2.5 py-1 rounded-full border border-gray-200">GENERAL</span>
                        <?php endif; ?>
                    </div>

                    <div class="relative h-48 bg-gray-50 rounded-2xl mb-4 overflow-hidden group-hover:bg-brand-50/30 transition-colors">
                        <img src="img/uploads/productos/<?= $p['imagen'] ?? 'default_product.png' ?>"
                            class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-500"
                            alt="<?= $p['nombre'] ?>">

                        <button onclick='agregarAlCarrito(<?= json_encode($p) ?>)'
                            class="absolute bottom-3 right-3 w-10 h-10 bg-brand-600 text-white rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300 hover:bg-brand-700 hover:scale-110">
                            <i class="ph-bold ph-plus text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 flex flex-col">
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1"><?= $p['categoria'] ?></p>
                        <h3 class="font-bold text-gray-900 text-lg leading-tight mb-2 line-clamp-2" title="<?= $p['nombre'] ?>"><?= $p['nombre'] ?></h3>

                        <div class="mt-auto pt-4 flex items-end justify-between border-t border-gray-50">
                            <div>
                                <p class="text-xs text-gray-400">Precio</p>
                                <p class="text-2xl font-bold text-brand-600">Bs. <?= number_format($p['precio_venta'], 2) ?></p>
                            </div>
                            <button onclick='agregarAlCarrito(<?= json_encode($p) ?>)' class="md:hidden w-8 h-8 bg-gray-100 text-gray-600 rounded-full flex items-center justify-center">
                                <i class="ph-bold ph-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div id="overlay" onclick="toggleCarrito()" class="fixed inset-0 bg-black/40 z-[60] hidden backdrop-blur-sm transition-opacity opacity-0"></div>

    <div id="cart-sidebar" class="fixed inset-y-0 right-0 w-full md:w-[450px] bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-[70] flex flex-col border-l border-gray-100">

        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Mi Lista</h2>
                <p class="text-sm text-gray-400">Productos seleccionados</p>
            </div>
            <button onclick="toggleCarrito()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-all">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-4" id="cart-items">
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-100">
            <div class="flex justify-between items-end mb-6">
                <span class="text-gray-500 font-medium">Total Estimado</span>
                <span class="text-3xl font-bold text-brand-600" id="cart-total">Bs. 0.00</span>
            </div>

            <button onclick="finalizarLista()" class="group w-full py-4 bg-brand-600 text-white rounded-xl font-bold text-lg shadow-lg shadow-brand-500/30 hover:bg-brand-700 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all flex justify-center items-center gap-3">
                <i class="ph-bold ph-qr-code text-2xl group-hover:scale-110 transition-transform"></i>
                Generar Resumen
            </button>
            <p class="text-xs text-center text-gray-400 mt-4 leading-relaxed">
                <i class="ph-fill ph-info mr-1"></i> Precios referenciales. El pago se realiza en mostrador.
            </p>
        </div>
    </div>

    <script>
        let carrito = [];

        function agregarAlCarrito(producto) {
            const existe = carrito.find(p => p.id_producto == producto.id_producto);
            if (existe) {
                existe.cantidad++;
            } else {
                carrito.push({
                    ...producto,
                    cantidad: 1,
                    precio: parseFloat(producto.precio_venta)
                });
            }
            actualizarUI();

            // Toast personalizado
            const Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    popup: 'rounded-xl shadow-lg border border-gray-100'
                },
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: 'success',
                title: 'Producto agregado'
            });
        }

        function actualizarUI() {
            const contenedor = document.getElementById('cart-items');
            const contador = document.getElementById('cart-count');
            const totalLbl = document.getElementById('cart-total');
            const totalItems = carrito.reduce((sum, i) => sum + i.cantidad, 0);

            contador.innerText = totalItems;
            contador.classList.toggle('scale-100', totalItems > 0);

            if (carrito.length === 0) {
                contenedor.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full text-center text-gray-400">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="ph ph-basket text-4xl text-gray-300"></i>
                        </div>
                        <p class="font-medium">Tu lista está vacía</p>
                        <p class="text-sm mt-1">Agrega productos para comenzar</p>
                    </div>`;
                totalLbl.innerText = 'Bs. 0.00';
                return;
            }

            contenedor.innerHTML = '';
            let total = 0;

            carrito.forEach((p, index) => {
                total += p.precio * p.cantidad;
                contenedor.innerHTML += `
                    <div class="flex gap-4 p-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:border-brand-200 transition-colors">
                        <div class="w-20 h-20 bg-gray-50 rounded-xl flex-shrink-0 p-2">
                            <img src="img/uploads/productos/${p.imagen || 'default_product.png'}" class="w-full h-full object-contain">
                        </div>
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm line-clamp-1">${p.nombre}</h4>
                                <p class="text-xs text-gray-400 mt-0.5">${p.categoria}</p>
                            </div>
                            <div class="flex justify-between items-end">
                                <span class="font-bold text-brand-600">Bs. ${(p.precio * p.cantidad).toFixed(2)}</span>
                                <div class="flex items-center gap-3 bg-gray-50 rounded-lg p-1">
                                    <button onclick="cambiarCant(${index}, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-xs hover:text-red-500 transition-colors"><i class="ph-bold ph-minus"></i></button>
                                    <span class="text-xs font-bold w-3 text-center">${p.cantidad}</span>
                                    <button onclick="cambiarCant(${index}, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-xs hover:text-brand-600 transition-colors"><i class="ph-bold ph-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
            totalLbl.innerText = 'Bs. ' + total.toFixed(2);
        }

        function cambiarCant(index, delta) {
            carrito[index].cantidad += delta;
            if (carrito[index].cantidad <= 0) carrito.splice(index, 1);
            actualizarUI();
        }

        function toggleCarrito() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('overlay');
            const isOpen = !sidebar.classList.contains('translate-x-full');

            if (isOpen) {
                sidebar.classList.add('translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            } else {
                overlay.classList.remove('hidden');
                // Force reflow
                void overlay.offsetWidth;
                overlay.classList.add('opacity-100');
                sidebar.classList.remove('translate-x-full');
            }
        }

        function filtrarProductos() {
            const texto = document.getElementById('buscador').value.toLowerCase();
            const cards = document.querySelectorAll('.producto-card');
            cards.forEach(card => {
                const nombre = card.getAttribute('data-nombre');
                const cat = card.getAttribute('data-categoria');
                if (nombre.includes(texto) || cat.includes(texto)) {
                    card.style.display = 'flex'; // Flex porque las cards son flex-col
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filtrarCategoria(filtro) {
            const cards = document.querySelectorAll('.producto-card');
            cards.forEach(card => {
                // Si es 'all', mostrar todo. Si no, buscar coincidencia parcial en el atributo data-categoria o tipo
                // Nota: Esto es simplificado. Lo ideal es tener clases específicas.
                // Para este ejemplo, asumimos que filtrarCategoria('medicamento') mostrará todo. 
                // En producción, añade data-tipo="MEDICAMENTO" al HTML de la card.
                card.style.display = 'flex';
            });
            // Implementa tu lógica de filtrado aquí similar a filtrarProductos
        }

        function finalizarLista() {
            if (carrito.length === 0) return;

            let htmlLista = '<div class="bg-gray-50 rounded-xl p-4 border border-gray-100 max-h-60 overflow-y-auto">';
            let total = 0;

            carrito.forEach(p => {
                total += p.precio * p.cantidad;
                htmlLista += `
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-0 text-sm">
                        <div class="flex gap-2 items-center">
                            <span class="font-bold bg-brand-100 text-brand-700 px-1.5 rounded text-xs">${p.cantidad}x</span>
                            <span class="text-gray-700 truncate max-w-[150px]">${p.nombre}</span>
                        </div>
                        <span class="font-medium text-gray-900">Bs. ${(p.precio * p.cantidad).toFixed(2)}</span>
                    </div>`;
            });
            htmlLista += `</div>
                <div class="mt-4 flex justify-between items-center pt-4 border-t border-gray-100">
                    <span class="text-gray-500">Total Aproximado</span>
                    <span class="text-2xl font-bold text-brand-600">Bs. ${total.toFixed(2)}</span>
                </div>`;

            toggleCarrito();

            Swal.fire({
                title: 'Resumen de Pedido',
                html: htmlLista,
                icon: 'info',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0ea5e9',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold'
                }
            });
        }
    </script>
</body>

</html>