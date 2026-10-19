<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmaPlus - Tu Salud Primero</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#0ea5e9',
                            600: '#0284c7',
                            900: '#0c4a6e'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans antialiased text-gray-700 bg-white">

    <nav class="fixed w-full z-50 bg-sky-100 backdrop-blur-md shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="index.php" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-emerald-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        <i class="ph-fill ph-first-aid"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-gray-800">Farma<span class="text-emerald-500">Plus</span></span>
                </a>

                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#inicio" class="text-gray-600 hover:text-brand-600 font-medium">Inicio</a>
                    <a href="#servicios" class="text-gray-600 hover:text-brand-600 font-medium">Servicios</a>
                    <a href="#ofertas" class="text-gray-600 hover:text-brand-600 font-medium">Ofertas</a>
                    <a href="#contacto" class="text-gray-600 hover:text-brand-600 font-medium">Contacto</a>
                    <a href="index.php?controller=catalogo&action=index" class="text-gray-600 hover:text-brand-600 font-medium">Catologo</a>
                    <a href="index.php?controller=auth&action=index"
                        class="px-5 py-2.5 bg-brand-600 text-white font-bold rounded-full hover:bg-brand-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="ph ph-user"></i>
                        Acceder
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section id="inicio" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">

                <div>
                    <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6">
                        Cuidamos lo que <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-emerald-500">más importa.</span>
                    </h1>
                    <p class="text-lg text-gray-500 mb-8 leading-relaxed">
                        Encuentra todos tus medicamentos, productos de cuidado personal y asesoría farmacéutica en un solo lugar. Envíos rápidos y seguros a tu domicilio.
                    </p>
                    <div class="flex gap-4">
                        <a href="#contacto" class="px-8 py-4 bg-gray-900 text-white rounded-xl font-bold hover:bg-gray-800 transition-all">
                            Contáctanos
                        </a>
                        <a href="#servicios" class="px-8 py-4 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition-all flex items-center gap-2">
                            <i class="ph ph-play-circle text-xl"></i>
                            Ver Servicios
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-brand-500 to-emerald-500 rounded-2xl opacity-20 blur-2xl transform rotate-3"></div>
                    <img src="https://images.unsplash.com/photo-1631549916768-4119b2e5f926?q=80&w=1000&auto=format&fit=crop"
                        alt="Farmacia Moderna"
                        class="relative rounded-2xl shadow-2xl transform -rotate-2 hover:rotate-0 transition-all duration-500 object-cover h-[500px] w-full">
                </div>
            </div>
        </div>
    </section>
    <section id="ofertas" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Nuevos Ingresos</h2>
                    <p class="mt-2 text-gray-500">Lo último en llegar a Farmacia FarmaPlus.</p>
                </div>
                <a href="index.php?controller=catalogo&action=index" class="text-brand-600 font-bold hover:underline">Ver catálogo completo</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php if (!empty($destacados)): ?>
                    <?php foreach ($destacados as $prod): ?>
                        <div class="group relative bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="h-48 bg-gray-50 rounded-xl overflow-hidden mb-4 relative">
                                <?= imagenProducto($prod, 'w-full h-full object-contain group-hover:scale-105 transition-transform duration-300') ?>

                                <?php if ($prod['stock'] <= 5): ?>
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wider">Pocas Unidades</span>
                                <?php endif; ?>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-brand-600 transition-colors"><?= $prod['nombre'] ?></h3>
                            <p class="text-sm text-gray-500 mb-4 line-clamp-2"><?= $prod['descripcion'] ?? 'Sin descripción' ?></p>

                            <div class="flex items-center justify-between">
                                <span class="text-xl font-bold text-gray-900">Bs. <?= number_format($prod['precio_venta'], 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="col-span-4 text-center text-gray-400">Pronto agregaremos productos.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <section id="servicios" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900">¿Por qué elegir FarmaPlus?</h2>
                <p class="mt-4 text-gray-500">Nos diferenciamos por la calidad de atención y rapidez.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-3xl mb-6">
                        <i class="ph ph-shield-check"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Calidad Garantizada</h3>
                    <p class="text-gray-500">Todos nuestros productos cuentan con registro sanitario y control de lotes.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="w-14 h-14 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-3xl mb-6">
                        <i class="ph ph-chats-circle"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Atención de Calidad</h3>
                    <p class="text-gray-500">Farmacéuticos calificados disponibles para resolver tus dudas.</p>
                </div>
            </div>
        </div>
    </section>

    <footer id="contacto" class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="ph ph-first-aid text-2xl text-emerald-500"></i>
                        <span class="text-xl font-bold">Farma<span class="text-emerald-500">Plus</span></span>
                    </div>
                    <p class="text-gray-400 max-w-sm">
                        Tu salud es nuestro compromiso. Trabajamos día a día para brindarte los mejores productos al mejor precio.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-emerald-400">Catálogo</a></li>
                        <li><a href="#" class="hover:text-emerald-400">Nosotros</a></li>

                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center gap-2"><i class="ph ph-phone"></i> +591 600-0000</li>
                        <li class="flex items-center gap-2"><i class="ph ph-map-pin"></i> Av. Principal #123</li>
                        <li class="flex items-center gap-2"><i class="ph ph-envelope"></i> contacto@farmavida.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500 text-sm">
                &copy; 2025 FarmaVida S.A. Todos los derechos reservados.
            </div>
        </div>
    </footer>

</body>

</html>