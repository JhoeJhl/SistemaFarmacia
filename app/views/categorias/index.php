<?php ob_start(); ?>

<div class="max-w-5xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Categorías</h1>
            <p class="text-gray-500 text-sm">Organiza tus productos por grupos.</p>
        </div>
        <button onclick="toggleModal('modal-crear')" class="bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 shadow-md transition-all">
            <i class="ph ph-plus-circle text-xl"></i>
            <span>Nueva Categoría</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($categorias as $cat): ?>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex justify-between items-center hover:shadow-md transition-all group relative overflow-hidden">

                <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-500 rounded-l-xl"></div>

                <div class="pl-3">
                    <h3 class="font-bold text-gray-800 text-lg"><?= $cat['nombre'] ?></h3>
                    <p class="text-xs text-gray-400 mt-1"><?= $cat['descripcion'] ?? 'Sin descripción' ?></p>
                </div>

                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick='cargarEdicion(<?= json_encode($cat) ?>)' class="p-2 text-brand-500 hover:bg-brand-50 rounded-lg transition-colors" title="Editar">
                        <i class="ph ph-pencil-simple text-xl"></i>
                    </button>

                    <button onclick="confirmarEliminacion('index.php?controller=categoria&action=eliminar&id=<?= $cat['id_categoria'] ?>')"
                        class="p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                        title="Eliminar">
                        <i class="ph ph-trash text-xl"></i>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<div id="modal-crear" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm transition-opacity">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 transform transition-transform scale-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Nueva Categoría</h3>
            <button onclick="toggleModal('modal-crear')" class="text-gray-400 hover:text-gray-600"><i class="ph ph-x text-2xl"></i></button>
        </div>

        <form action="index.php?controller=categoria&action=guardar" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre</label>
                <input type="text" name="nombre" required placeholder="Ej. Antibióticos" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="2" placeholder="Breve descripción..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none"></textarea>
            </div>
            <button type="submit" class="w-full bg-brand-600 text-white font-bold py-3 rounded-lg hover:bg-brand-700 transition">Guardar</button>
        </form>
    </div>
</div>

<div id="modal-editar" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-brand-600">Editar Categoría</h3>
            <button onclick="toggleModal('modal-editar')" class="text-gray-400 hover:text-gray-600"><i class="ph ph-x text-2xl"></i></button>
        </div>

        <form action="index.php?controller=categoria&action=actualizar" method="POST" class="space-y-4">
            <input type="hidden" name="id_categoria" id="edit_id">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre</label>
                <input type="text" name="nombre" id="edit_nombre" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                <textarea name="descripcion" id="edit_descripcion" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand-500 outline-none"></textarea>
            </div>
            <button type="submit" class="w-full bg-brand-600 text-white font-bold py-3 rounded-lg hover:bg-brand-700 transition">Actualizar Cambios</button>
        </form>
    </div>
</div>
<div id="modal-eliminar" class="fixed inset-0 bg-black/60 hidden z-[60] flex items-center justify-center backdrop-blur-[2px] transition-opacity duration-300">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 relative transform transition-all scale-100">

        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <i class="ph ph-warning text-3xl text-red-600"></i>
            </div>

            <h3 class="text-lg font-bold text-gray-900">¿Estás seguro?</h3>
            <p class="text-sm text-gray-500 mt-2">
                Esta acción eliminará la categoría. Si tiene productos asociados, estos podrían quedar sin categoría.
            </p>
        </div>

        <div class="mt-6 flex gap-3">
            <button onclick="cerrarModalEliminar()" class="w-full bg-white text-gray-700 border border-gray-300 font-bold py-2.5 rounded-xl hover:bg-gray-50 transition">
                Cancelar
            </button>
            <button id="btn-confirmar-eliminar" class="w-full bg-red-600 text-white font-bold py-2.5 rounded-xl hover:bg-red-700 shadow-lg shadow-red-500/30 transition">
                Sí, Eliminar
            </button>
        </div>
    </div>
</div>
<script>
    // Abrir/Cerrar Modales
    function toggleModal(id) {
        const el = document.getElementById(id);
        el.classList.toggle('hidden');
    }

    // Llenar formulario de edición
    function cargarEdicion(categoria) {
        document.getElementById('edit_id').value = categoria.id_categoria;
        document.getElementById('edit_nombre').value = categoria.nombre;
        document.getElementById('edit_descripcion').value = categoria.descripcion;

        toggleModal('modal-editar');
    }
    let urlParaEliminar = ''; // Variable global temporal

    // 1. Función que llama el botón de la tabla
    function confirmarEliminacion(url) {
        urlParaEliminar = url; // Guardamos la URL
        const modal = document.getElementById('modal-eliminar');
        modal.classList.remove('hidden');
    }

    // 2. Función para cerrar el modal
    function cerrarModalEliminar() {
        const modal = document.getElementById('modal-eliminar');
        modal.classList.add('hidden');
        urlParaEliminar = ''; // Limpiamos
    }

    // 3. Evento del botón "Sí, Eliminar"
    document.getElementById('btn-confirmar-eliminar').addEventListener('click', function() {
        if (urlParaEliminar) {
            window.location.href = urlParaEliminar; // Redirigimos al controlador
        }
    });

    // ---------------------------------------------
    // TUS OTRAS FUNCIONES (Toggle, Cargar, etc.)
    // ---------------------------------------------
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function cargarEdicion(categoria) {
        document.getElementById('edit_id').value = categoria.id_categoria;
        document.getElementById('edit_nombre').value = categoria.nombre;
        document.getElementById('edit_descripcion').value = categoria.descripcion;
        toggleModal('modal-editar');
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>