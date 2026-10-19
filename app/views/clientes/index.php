<?php ob_start(); ?>
<?php $esc = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>

<div class="max-w-7xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Clientes</h1>
            <p class="text-gray-500 text-sm">Registra a tus clientes para asociarlos a las ventas.</p>
        </div>
        <button onclick="abrirCrear()" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md transition-all">
            <i class="ph ph-plus-circle text-xl"></i>
            <span>Nuevo Cliente</span>
        </button>
    </div>

    <div class="mb-4">
        <div class="relative max-w-sm">
            <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="buscador" oninput="filtrarClientes()" placeholder="Buscar por nombre, documento o teléfono..."
                class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Documento</th>
                        <th class="px-4 py-3">Teléfono</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3">Dirección</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-clientes" class="divide-y divide-gray-100">
                    <?php if (empty($clientes)): ?>
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No hay clientes registrados.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($clientes as $c): ?>
                        <?php $protegido = (int)$c['id_cliente'] === Cliente::ID_PUBLICO_GENERAL; ?>
                        <tr class="hover:bg-gray-50 fila-cliente" data-busqueda="<?= $esc(mb_strtolower($c['nombre'] . ' ' . $c['documento'] . ' ' . $c['telefono'])) ?>">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold uppercase text-xs">
                                        <?= $esc(mb_substr($c['nombre'], 0, 2)) ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800"><?= $esc($c['nombre']) ?></p>
                                        <?php if ($protegido): ?>
                                            <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full font-bold">Por defecto en ventas</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600"><?= $esc($c['documento'] ?: '—') ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= $esc($c['telefono'] ?: '—') ?></td>
                            <td class="px-4 py-3 text-gray-600"><?= $esc(($c['correo'] ?: $c['email']) ?: '—') ?></td>
                            <td class="px-4 py-3 text-gray-600 max-w-[200px] truncate"><?= $esc($c['direccion'] ?: '—') ?></td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center gap-1">
                                    <button onclick="abrirEditar(this)" data-cliente="<?= $esc(json_encode([
                                        'id_cliente' => $c['id_cliente'],
                                        'documento'  => $c['documento'],
                                        'nombre'     => $c['nombre'],
                                        'telefono'   => $c['telefono'],
                                        'correo'     => $c['correo'] ?: $c['email'],
                                        'direccion'  => $c['direccion'],
                                    ])) ?>" class="p-2 text-brand-500 hover:bg-brand-50 rounded-lg transition-colors" title="Editar">
                                        <i class="ph ph-pencil-simple text-lg"></i>
                                    </button>
                                    <?php if (!$protegido): ?>
                                        <button onclick="confirmarEliminacion(<?= (int)$c['id_cliente'] ?>)" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                            <i class="ph ph-trash text-lg"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-cliente" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-titulo" class="text-xl font-bold text-gray-800">Nuevo Cliente</h3>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600"><i class="ph ph-x text-2xl"></i></button>
        </div>

        <form id="form-cliente" action="index.php?controller=cliente&action=guardar" method="POST" class="space-y-4">
            <input type="hidden" name="id_cliente" id="f_id">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre completo *</label>
                <input type="text" name="nombre" id="f_nombre" required maxlength="150" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">CI / NIT</label>
                    <input type="text" name="documento" id="f_documento" maxlength="50" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" id="f_telefono" maxlength="50" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Correo</label>
                <input type="email" name="correo" id="f_correo" maxlength="150" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Dirección</label>
                <textarea name="direccion" id="f_direccion" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none"></textarea>
            </div>
            <button type="submit" id="btn-guardar" class="w-full bg-brand-600 text-white font-bold py-3 rounded-lg hover:bg-brand-700 transition">Guardar Cliente</button>
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
        document.getElementById('btn-guardar').innerText = 'Guardar Cliente';
        modal.classList.remove('hidden');
    }

    function abrirEditar(boton) {
        const c = JSON.parse(boton.dataset.cliente);
        document.getElementById('f_id').value = c.id_cliente;
        campos.slice(1).forEach(campo => document.getElementById('f_' + campo).value = c[campo] ?? '');
        form.action = 'index.php?controller=cliente&action=actualizar';
        document.getElementById('modal-titulo').innerText = 'Editar Cliente';
        document.getElementById('btn-guardar').innerText = 'Actualizar Cambios';
        modal.classList.remove('hidden');
    }

    function cerrarModal() {
        modal.classList.add('hidden');
    }

    function filtrarClientes() {
        const texto = document.getElementById('buscador').value.toLowerCase();
        document.querySelectorAll('.fila-cliente').forEach(fila => {
            fila.style.display = fila.dataset.busqueda.includes(texto) ? '' : 'none';
        });
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
