<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmaVida - Sistema de Gestión</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased overflow-hidden">

    <div class="flex h-screen w-full">

        <div id="mobile-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>

        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-brand-950 text-white flex flex-col shadow-2xl transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out border-r border-brand-800">

            <div class="h-16 flex items-center px-6 bg-brand-900 border-b border-brand-800 justify-between lg:justify-start">
                <a href="index.php" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-emerald-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        <i class="ph-fill ph-first-aid"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-gray-800">Farma<span class="text-emerald-500">Plus</span></span>
                </a>
                <button onclick="toggleSidebar()" class="lg:hidden text-brand-300 hover:text-white">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto no-scrollbar text-sm font-medium">

                <p class="px-3 text-[10px] font-bold text-brand-400 uppercase tracking-widest mb-2 mt-2">Operaciones</p>

                <a href="index.php?controller=dashboard&action=index"
                    class="flex items-center px-3 py-2.5 text-brand-100 hover:bg-brand-800 hover:text-white rounded-lg transition-all group">
                    <i class="ph ph-squares-four text-xl mr-3 text-brand-400 group-hover:text-emerald-400"></i>
                    Dashboard
                </a>

                <a href="index.php?controller=venta&action=crear"
                    class="flex items-center px-3 py-3 mt-2 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white hover:shadow-lg hover:shadow-emerald-900/20 rounded-lg transition-all group transform hover:scale-[1.02]">
                    <i class="ph-bold ph-shopping-cart text-xl mr-3 animate-pulse"></i>
                    <span class="font-bold">Nueva Venta</span>
                </a>

                <a href="index.php?controller=venta&action=historial"
                    class="flex items-center px-3 py-2.5 text-brand-100 hover:bg-brand-800 hover:text-white rounded-lg transition-all group mt-2">
                    <i class="ph ph-receipt text-xl mr-3 text-brand-400 group-hover:text-emerald-400"></i>
                    Historial Ventas
                </a>

                <?php if (function_exists('isAdmin') && isAdmin()): ?>
                    <a href="index.php?controller=bitacora&action=index"
                        class="flex items-center px-3 py-2.5 text-brand-100 hover:bg-brand-800 hover:text-white rounded-lg transition-all group">
                        <i class="ph ph-shield-check text-xl mr-3 text-brand-400 group-hover:text-emerald-400"></i>
                        Auditoría
                    </a>
                <?php endif; ?>

                <p class="px-3 text-[10px] font-bold text-brand-400 uppercase tracking-widest mb-2 mt-6">Logística</p>

                <a href="index.php?controller=producto&action=index"
                    class="flex items-center px-3 py-2.5 text-brand-100 hover:bg-brand-800 hover:text-white rounded-lg transition-all group">
                    <i class="ph ph-package text-xl mr-3 text-brand-400 group-hover:text-emerald-400"></i>
                    Inventario
                </a>

                <a href="index.php?controller=compra&action=crear"
                    class="flex items-center px-3 py-2.5 text-brand-100 hover:bg-brand-800 hover:text-white rounded-lg transition-all group">
                    <i class="ph ph-truck text-xl mr-3 text-brand-400 group-hover:text-emerald-400"></i>
                    Compras
                </a>

                <a href="index.php?controller=proveedor&action=index"
                    class="flex items-center px-3 py-2.5 text-brand-100 hover:bg-brand-800 hover:text-white rounded-lg transition-all group">
                    <i class="ph ph-address-book text-xl mr-3 text-brand-400 group-hover:text-emerald-400"></i>
                    Proveedores
                </a>

                <p class="px-3 text-[10px] font-bold text-brand-400 uppercase tracking-widest mb-2 mt-6">Administración</p>

                <a href="index.php?controller=usuario&action=index"
                    class="flex items-center px-3 py-2.5 text-brand-100 hover:bg-brand-800 hover:text-white rounded-lg transition-all group">
                    <i class="ph ph-user-gear text-xl mr-3 text-brand-400 group-hover:text-emerald-400"></i>
                    Usuarios
                </a>

                <a href="index.php?controller=categoria&action=index"
                    class="flex items-center px-3 py-2.5 text-brand-100 hover:bg-brand-800 hover:text-white rounded-lg transition-all group">
                    <i class="ph ph-tag text-xl mr-3 text-brand-400 group-hover:text-emerald-400"></i>
                    Categorías
                </a>

            </nav>

            <div class="p-4 border-t border-brand-800 bg-brand-900">
                <a href="index.php?controller=auth&action=logout"
                    class="flex items-center justify-center w-full px-4 py-2.5 text-sm text-red-200 hover:text-white hover:bg-red-500/20 rounded-lg transition-colors border border-transparent hover:border-red-500/30">
                    <i class="ph ph-sign-out text-lg mr-2"></i>
                    Cerrar Sesión
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50 relative">

            <header class="h-16 bg-white/80 backdrop-blur-md shadow-sm flex items-center justify-between px-6 z-10 border-b border-gray-200">

                <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-brand-600 p-2 -ml-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="ph ph-list text-2xl"></i>
                </button>

                <div class="hidden sm:block">
                    <h2 class="text-lg font-bold text-gray-700 tracking-tight">Sistema de Gestión</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block leading-tight">
                        <p class="text-sm font-bold text-gray-800">
                            <?= $_SESSION['user_name'] ?? 'Usuario' ?>
                        </p>
                        <p class="text-[10px] text-brand-600 font-bold uppercase tracking-wide bg-brand-50 px-2 py-0.5 rounded-full inline-block mt-0.5">
                            <?= $_SESSION['user_rol'] ?? 'Invitado' ?>
                        </p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center text-brand-700 font-bold border-2 border-white shadow-md">
                        <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6 pb-20 scroll-smooth">
                <?php
                if (isset($content)) {
                    echo $content;
                } else {
                    echo "<div class='flex flex-col items-center justify-center h-full text-gray-400'>
                                <i class='ph ph-ghost text-6xl mb-4'></i>
                                <p>No hay contenido para mostrar.</p>
                              </div>";
                }
                ?>
            </main>
        </div>

    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');

            if (sidebar.classList.contains('-translate-x-full')) {
                // Abrir
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.remove('opacity-0');
                }, 10);
            } else {
                // Cerrar
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }
        }
    </script>

</body>

</html>