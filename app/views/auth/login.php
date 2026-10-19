<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - FarmaPlus</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
                            500: '#0ea5e9', // Azul Primario
                            600: '#0284c7', // Azul Hover
                            900: '#0c4a6e', // Azul Oscuro
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.5s ease-out forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(10px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            },
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased selection:bg-brand-500 selection:text-white">

    <div class="min-h-screen flex w-full">

        <div class="hidden lg:flex w-[55%] relative overflow-hidden bg-brand-900 items-end justify-start p-16">

            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1576602976047-174e57a47881?q=80&w=1920&auto=format&fit=crop"
                    class="w-full h-full object-cover opacity-30 mix-blend-luminosity"
                    alt="Farmacia Moderna">
                <div class="absolute inset-0 bg-gradient-to-tr from-brand-900 via-brand-900/80 to-transparent"></div>
            </div>

            <div class="relative z-10 w-full max-w-lg">

                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-2xl shadow-2xl">
                    <a href="index.php" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-brand-50 text-emerald-500 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <i class="ph-fill ph-first-aid"></i>
                        </div>
                        <span class="text-2xl font-bold tracking-tight text-gray-800">Farma<span class="text-emerald-500">Plus</span></span>
                    </a>
                    <h3 class="text-2xl font-bold text-white mb-1 leading-tight">
                        Sistema de Gestión farmacéutica
                    </h3>
                    <p class="text-blue-100 text-sm leading-relaxed">
                        Controla inventarios, optimiza ventas y toma decisiones basadas en datos reales.
                    </p>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-[45%] flex flex-col justify-center items-center p-8 bg-white shadow-2xl z-20">

            <div class="w-full max-w-md animate-fade-in-up">

                <div class="flex lg:hidden mb-8 items-center gap-2 text-brand-600">
                    <i class="ph ph-first-aid text-3xl"></i>
                    <span class="text-xl font-bold tracking-tight">FarmaPlus</span>
                </div>

                <div class="mb-10">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Bienvenido</h1>
                    <p class="text-gray-500 mt-2 text-sm">Por favor, ingresa tus credenciales para continuar.</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="mb-6 bg-red-50 border border-red-100 rounded-xl p-4 flex items-start gap-3 animate-pulse">
                        <i class="ph-fill ph-warning-circle text-red-500 text-xl mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-bold text-red-700">Error de Autenticación</h4>
                            <p class="text-xs text-red-600 mt-0.5">El correo o la contraseña no coinciden.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="index.php?controller=auth&action=login" method="POST" class="space-y-5">

                    <div class="group">
                        <label for="correo" class="block text-sm font-semibold text-gray-700 mb-1.5 ml-1">Correo Electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="ph ph-envelope text-gray-400 text-lg group-focus-within:text-brand-500 transition-colors"></i>
                            </div>
                            <input type="email" name="correo" id="correo" required
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all placeholder:text-gray-400"
                                placeholder="nombre@farmacia.com">
                        </div>
                    </div>

                    <div class="group">
                        <div class="flex justify-between items-center mb-1.5 ml-1">
                            <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="ph ph-lock-key text-gray-400 text-lg group-focus-within:text-brand-500 transition-colors"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all placeholder:text-gray-400"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-gradient-to-r from-brand-600 to-brand-500 hover:from-brand-700 hover:to-brand-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-500/30 hover:shadow-brand-500/40 transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                        <span>Ingresar</span>
                        <i class="ph-bold ph-arrow-right"></i>
                    </button>

                </form>

                <div class="mt-10 text-center">
                    <p class="text-xs text-gray-400">
                        &copy; 2025 FarmaVida System. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>

    </div>
</body>

</html>