<?php ob_start(); ?>
<?php $esc = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>

<div class="max-w-7xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                <i class="ph-duotone ph-users-three text-3xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Clientes</h1>
                <p class="text-gray-500 text-sm">Registra a tus clientes para asociarlos a las ventas.</p>
            </div>
        </div>
        <button onclick="abrirCrear()" class="bg-brand-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 shadow-md transition-all">
            <i class="ph ph-user-plus text-xl"></i>
            <span>Nuevo Cliente</span>
        </button>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
        <div class="relative w-full sm:max-w-sm">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
            <input type="text" id="buscador" oninput="filtrarClientes()" placeholder="Buscar por nombre, documento o teléfono..."
                class="w-full pl-10 pr-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 outline-none shadow-sm">
        </div>
        <p class="text-sm text-gray-500 flex items-center gap-1.5">
            <i class="ph ph-address-book text-lg text-sky-500"></i>
            <span id="contador-clientes"><?= count($clientes) ?></span> clientes
        </p>
    </div>

    <div id="lista-clientes" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($clientes as $c): ?>
            <?php
            $protegido = (int)$c['id_cliente'] === Cliente::ID_PUBLICO_GENERAL;
            $correo = $c['correo'] ?: $c['email'];
            $telefonoWa = preg_replace('/[^0-9]/', '', (string)$c['telefono']);
            ?>
            <div class="tarjeta-cliente bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group relative"
                data-busqueda="<?= $esc(mb_strtolower($c['nombre'] . ' ' . $c['documento'] . ' ' . $c['telefono'])) ?>">

                <div class="flex justify-between items-start mb-4">
                    <div class="h-12 w-12 rounded-full <?= $protegido ? 'bg-emerald-50 text-emerald-600' : 'bg-sky-50 text-sky-600' ?> flex items-center justify-center">
                        <i class="ph-duotone <?= $protegido ? 'ph-storefront' : 'ph-user' ?> text-2xl"></i>
                    </div>
                    <div class="flex gap-1 opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="abrirEditar(this)" data-cliente="<?= $esc(json_encode([
                            'id_cliente' => $c['id_cliente'],
                            'documento'  => $c['documento'],
                            'nombre'     => $c['nombre'],
                            'telefono'   => $c['telefono'],
                            'correo'     => $correo,
                            'direccion'  => $c['direccion'],
                        ])) ?>" class="p-1.5 text-gray-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors" title="Editar">
                            <i class="ph ph-pencil-simple text-xl"></i>
                        </button>
                        <?php if (!$protegido): ?>
                            <button onclick="confirmarEliminacion(<?= (int)$c['id_cliente'] ?>)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                <i class="ph ph-trash text-xl"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <h3 class="font-bold text-gray-800 text-lg mb-1 line-clamp-1" title="<?= $esc($c['nombre']) ?>"><?= $esc($c['nombre']) ?></h3>
                <?php if ($protegido): ?>
                    <p class="text-xs text-emerald-600 font-semibold mb-4 flex items-center gap-1">
                        <i class="ph-fill ph-seal-check"></i> Cliente por defecto en ventas
                    </p>
                <?php else: ?>
                    <p class="text-xs text-gray-500 mb-4 flex items-center gap-1">
                        <i class="ph ph-identification-card"></i> <?= $c['documento'] ? 'CI/NIT: ' . $esc($c['documento']) : 'Sin documento' ?>
                    </p>
                <?php endif; ?>

                <div class="space-y-2 border-t border-gray-100 pt-4 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-phone text-sky-500 text-lg"></i>
                        <span><?= $esc($c['telefono'] ?: 'Sin teléfono') ?></span>
                        <?php if ($telefonoWa !== '' && !$protegido): ?>
                            <a href="https://wa.me/<?= $telefonoWa ?>" target="_blank" title="Escribir por WhatsApp"
                                class="ml-auto text-green-600 hover:bg-green-50 p-1 rounded-lg">
                                <i class="ph ph-whatsapp-logo text-xl"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="ph ph-envelope-simple text-sky-500 text-lg shrink-0"></i>
                        <?php if ($correo): ?>
                            <a href="mailto:<?= $esc($correo) ?>" class="hover:text-sky-600 truncate"><?= $esc($correo) ?></a>
                        <?php else: ?>
                            <span class="text-gray-400">Sin correo</span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2 min-w-0">
                        <i class="ph ph-map-pin text-sky-500 text-lg shrink-0"></i>
                        <span class="truncate <?= $c['direccion'] ? '' : 'text-gray-400' ?>"><?= $esc($c['direccion'] ?: 'Sin dirección') ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="sin-resultados" class="<?= empty($clientes) ? '' : 'hidden' ?> text-center py-16 text-gray-400 flex flex-col items-center">
        <i class="ph-duotone ph-user-circle-dashed text-6xl mb-3 text-sky-300"></i>
        <p><?= empty($clientes) ? 'No hay clientes registrados.' : 'No se encontraron clientes con ese criterio.' ?></p>
    </div>
</div>

<div id="modal-cliente" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 mx-4">
        <div class="flex justify-between items-center mb-5">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i id="modal-icono" class="ph ph-user-plus text-xl"></i>
                </div>
                <h3 id="modal-titulo" class="text-xl font-bold text-gray-800">Nuevo Cliente</h3>
            </div>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600"><i class="ph ph-x text-2xl"></i></button>
        </div>

        <form id="form-cliente" action="index.php?controller=cliente&action=guardar" method="POST" class="space-y-4">
            <input type="hidden" name="id_cliente" id="f_id">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre completo *</label>
                <div class="relative">
                    <i class="ph ph-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="text" name="nombre" id="f_nombre" required maxlength="150" class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">CI / NIT</label>
                    <div class="relative">
                        <i class="ph ph-identification-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input type="text" name="documento" id="f_documento" maxlength="50" class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Teléfono</label>
                    <div class="relative">
                        <i class="ph ph-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                        <input type="text" name="telefono" id="f_telefono" maxlength="50" class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Correo</label>
                <div class="relative">
                    <i class="ph ph-envelope-simple absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></i>
                    <input type="email" name="correo" id="f_correo" maxlength="150" class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Dirección</label>
                <div class="relative">
                    <i class="ph ph-map-pin absolute left-3 top-3 text-gray-400 text-lg"></i>
                    <textarea name="direccion" id="f_direccion" rows="2" class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none"></textarea>
                </div>
            </div>
            <button type="submit" class="w-full bg-brand-600 text-white font-bold py-3 rounded-lg hover:bg-sky-700 transition flex items-center justify-center gap-2">
                <i class="ph ph-floppy-disk text-xl"></i>
                <span id="btn-guardar">Guardar Cliente</span>
            </button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('modal-cliente');
    const form = document.getElementById('form-cliente');
    const campos = ['id', 'nombre', 'documento', 'telefono', 'correo', 'direccion'];

    function abrirCrear() {
        form.reset();
        document.getElementById('f_id').value = '';
        form.action = 'index.php?controller=cliente&action=guardar';
        document.getElementById('modal-titulo').innerText = 'Nuevo Cliente';
        document.getElementById('modal-icono').className = 'ph ph-user-plus text-xl';
        document.getElementById('btn-guardar').innerText = 'Guardar Cliente';
        modal.classList.remove('hidden');
    }

    function abrirEditar(boton) {
        const c = JSON.parse(boton.dataset.cliente);
        document.getElementById('f_id').value = c.id_cliente;
        campos.slice(1).forEach(campo => document.getElementById('f_' + campo).value = c[campo] ?? '');
        form.action = 'index.php?controller=cliente&action=actualizar';
        document.getElementById('modal-titulo').innerText = 'Editar Cliente';
        document.getElementById('modal-icono').className = 'ph ph-user-gear text-xl';
        document.getElementById('btn-guardar').innerText = 'Actualizar Cambios';
        modal.classList.remove('hidden');
    }

    function cerrarModal() {
        modal.classList.add('hidden');
    }

    function filtrarClientes() {
        const texto = document.getElementById('buscador').value.toLowerCase();
        let visibles = 0;
        document.querySelectorAll('.tarjeta-cliente').forEach(tarjeta => {
            const coincide = tarjeta.dataset.busqueda.includes(texto);
            tarjeta.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });
        document.getElementById('contador-clientes').innerText = visibles;
        document.getElementById('sin-resultados').classList.toggle('hidden', visibles > 0);
    }

    function confirmarEliminacion(id) {
        Swal.fire({
            title: '¿Eliminar cliente?',
            text: 'Dejará de aparecer en la lista y en el punto de venta. Sus ventas anteriores se conservan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc2626'
        }).then(r => {
            if (r.isConfirmed) {
                window.location.href = `index.php?controller=cliente&action=eliminar&id=${id}`;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const mensajes = {
            creado: ['success', 'Cliente registrado'],
            actualizado: ['success', 'Cliente actualizado'],
            eliminado: ['success', 'Cliente eliminado'],
            protegido: ['info', 'El cliente "Público General" no se puede eliminar'],
            error: ['error', 'No se pudo completar la operación']
        };
        const msg = new URLSearchParams(window.location.search).get('msg');
        if (mensajes[msg]) {
            Swal.fire({ icon: mensajes[msg][0], title: mensajes[msg][1], timer: 2000, showConfirmButton: msg === 'error' });
            window.history.replaceState({}, document.title, window.location.pathname + '?controller=cliente&action=index');
        }
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>
