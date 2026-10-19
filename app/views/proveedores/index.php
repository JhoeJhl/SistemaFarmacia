<?php ob_start(); ?>
<?php $esc = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>

<div class="max-w-7xl mx-auto">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Proveedores</h1>
            <p class="text-gray-500 text-sm">Gestiona tus contactos y laboratorios.</p>
        </div>
        <button onclick="abrirCrear()" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md transition-all">
            <i class="ph ph-plus-circle text-xl"></i>
            <span>Nuevo Proveedor</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($proveedores as $prov): ?>
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group relative">
            
            <div class="flex justify-between items-start mb-4">
                <div class="h-12 w-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold uppercase">
                    <?= $esc(mb_substr($prov['nombre'], 0, 2)) ?>
                </div>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="abrirEditar(this)" data-proveedor="<?= $esc(json_encode([
                        'id_proveedor' => $prov['id_proveedor'],
                        'nombre' => $prov['nombre'],
                        'telefono' => $prov['telefono'],
                        'correo' => $prov['correo'],
                        'direccion' => $prov['direccion'],
                    ])) ?>" class="text-gray-300 hover:text-brand-600 p-1" title="Editar">
                        <i class="ph ph-pencil-simple text-xl"></i>
                    </button>
                    <a href="index.php?controller=proveedor&action=eliminar&id=<?= $prov['id_proveedor'] ?>" 
                       onclick="return confirm('¿Eliminar este proveedor?')"
                       class="text-gray-300 hover:text-red-500 p-1">
                        <i class="ph ph-trash text-xl"></i>
                    </a>
                </div>
            </div>

            <h3 class="font-bold text-gray-800 text-lg mb-1"><?= $esc($prov['nombre']) ?></h3>
            <p class="text-xs text-gray-500 mb-4 flex items-center gap-1">
                <i class="ph ph-map-pin"></i> <?= $esc($prov['direccion'] ?: 'Sin dirección') ?>
            </p>

            <div class="space-y-2 border-t border-gray-100 pt-4">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="ph ph-phone text-brand-500"></i>
                    <span><?= $esc($prov['telefono']) ?></span>
                    <?php if($prov['telefono']): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $prov['telefono']) ?>" target="_blank" class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold hover:bg-green-200">
                            WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="ph ph-envelope text-brand-500"></i>
                    <a href="mailto:<?= $esc($prov['correo']) ?>" class="hover:text-brand-600"><?= $esc($prov['correo']) ?></a>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

</div>

<div id="modal-proveedor" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl transform scale-95 transition-transform duration-300 p-6">
        
        <div class="flex justify-between items-center mb-6">
            <h3 id="modal-titulo" class="text-xl font-bold text-gray-800">Registrar Proveedor</h3>
            <button onclick="toggleModal('modal-proveedor')" class="text-gray-400 hover:text-gray-600">
                <i class="ph ph-x text-2xl"></i>
            </button>
        </div>

        <form id="form-proveedor" action="index.php?controller=proveedor&action=guardar" method="POST" class="space-y-4">
            <input type="hidden" name="id_proveedor" id="f_id">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Empresa / Laboratorio</label>
                <input type="text" name="nombre" id="f_nombre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" id="f_telefono" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                    <input type="email" name="correo" id="f_correo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <textarea name="direccion" id="f_direccion" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none"></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" id="btn-guardar" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 rounded-lg shadow transition-colors">
                    Guardar Proveedor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirCrear() {
        const form = document.getElementById('form-proveedor');
        form.reset();
        document.getElementById('f_id').value = '';
        form.action = 'index.php?controller=proveedor&action=guardar';
        document.getElementById('modal-titulo').innerText = 'Registrar Proveedor';
        document.getElementById('btn-guardar').innerText = 'Guardar Proveedor';
        toggleModal('modal-proveedor');
    }

    function abrirEditar(boton) {
        const p = JSON.parse(boton.dataset.proveedor);
        const form = document.getElementById('form-proveedor');
        document.getElementById('f_id').value = p.id_proveedor;
        ['nombre', 'telefono', 'correo', 'direccion'].forEach(c => document.getElementById('f_' + c).value = p[c] ?? '');
        form.action = 'index.php?controller=proveedor&action=actualizar';
        document.getElementById('modal-titulo').innerText = 'Editar Proveedor';
        document.getElementById('btn-guardar').innerText = 'Actualizar Cambios';
        toggleModal('modal-proveedor');
    }

    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = modal.querySelector('div'); // El contenedor blanco

        if (modal.classList.contains('hidden')) {
            // Abrir
            modal.classList.remove('hidden');
            // Pequeño delay para permitir que el navegador renderice antes de animar opacidad
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        } else {
            // Cerrar
            modal.classList.add('opacity-0');
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300); // Esperar a que termine la animación
        }
    }
</script>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php'; 
?>