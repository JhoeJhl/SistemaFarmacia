<?php ob_start(); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-6xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="ph ph-users-three text-brand-500"></i> Gestión de Personal
            </h1>
            <p class="text-gray-500 text-sm mt-1">Administra accesos y roles del sistema.</p>
        </div>
        <button onclick="toggleModal('modal-crear')" class="bg-brand-600 hover:bg-brand-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2">
            <i class="ph ph-user-plus text-lg"></i>
            <span>Nuevo Colaborador</span>
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs border-b border-gray-100">
                    <tr>
                        <th class="p-5 pl-6">Usuario</th>
                        <th class="p-5">Rol</th>
                        <th class="p-5">Correo Electrónico</th>
                        <th class="p-5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if(empty($usuarios)): ?>
                        <tr><td colspan="4" class="p-8 text-center text-gray-400">No hay usuarios registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr class="hover:bg-brand-50/30 transition-colors group">
                                <td class="p-5 pl-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center font-bold text-lg border-2 border-white shadow-sm">
                                            <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800"><?= $u['nombre'] ?></p>
                                            <p class="text-xs text-gray-400">ID: #<?= $u['id_usuario'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5">
                                    <?php if($u['rol'] == 'admin'): ?>
                                        <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 border border-purple-100 px-2.5 py-1 rounded-full text-xs font-bold shadow-sm">
                                            <i class="ph-fill ph-crown"></i> Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-100 px-2.5 py-1 rounded-full text-xs font-bold">
                                            <i class="ph-fill ph-user"></i> Vendedor
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-5 text-gray-600 font-medium"><?= $u['correo'] ?></td>
                                
                                <td class="p-5 text-center">
                                    <div class="flex justify-center gap-2 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick='editarUsuario(<?= json_encode($u) ?>)'
                                                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-brand-600 hover:bg-white hover:shadow-md border border-transparent hover:border-gray-100 transition-all" 
                                                title="Editar">
                                            <i class="ph ph-pencil-simple text-lg"></i>
                                        </button>

                                        <?php if ($u['id_usuario'] != $_SESSION['user_id']): ?>
                                            <button onclick="confirmarEliminacion(<?= $u['id_usuario'] ?>)"
                                               class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-red-600 hover:bg-white hover:shadow-md border border-transparent hover:border-gray-100 transition-all" 
                                               title="Eliminar">
                                                <i class="ph ph-trash text-lg"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-crear" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-0 overflow-hidden transform scale-100 transition-transform duration-300">
        
        <div class="bg-gray-50 p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Nuevo Usuario</h3>
            <button onclick="toggleModal('modal-crear')" class="text-gray-400 hover:text-gray-600"><i class="ph ph-x text-xl"></i></button>
        </div>

        <form action="index.php?controller=usuario&action=guardar" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre Completo</label>
                <input type="text" name="nombre" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all" placeholder="Ej. Juan Perez">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Correo Electrónico</label>
                <input type="email" name="correo" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all" placeholder="usuario@farmacia.com">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Contraseña</label>
                    <input type="password" name="password" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rol</label>
                    <select name="rol" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none bg-white">
                        <option value="vendedor">Vendedor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>
            <div class="pt-2">
                <button type="submit" class="w-full bg-brand-600 text-white font-bold py-3 rounded-xl hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition-all flex justify-center items-center gap-2">
                    <i class="ph-bold ph-check"></i> Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modal-editar" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-0 overflow-hidden transform scale-100 transition-transform duration-300">
        
        <div class="bg-gray-50 p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-brand-700">Editar Usuario</h3>
            <button onclick="toggleModal('modal-editar')" class="text-gray-400 hover:text-gray-600"><i class="ph ph-x text-xl"></i></button>
        </div>

        <form action="index.php?controller=usuario&action=actualizar" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id_usuario" id="edit_id">

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nombre Completo</label>
                <input type="text" name="nombre" id="edit_nombre" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Correo</label>
                <input type="email" name="correo" id="edit_correo" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Contraseña</label>
                    <input type="password" name="password" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all" placeholder="●●●●●●">
                    <p class="text-[10px] text-gray-400 mt-1">Dejar vacío para mantener.</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rol</label>
                    <select name="rol" id="edit_rol" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none bg-white">
                        <option value="vendedor">Vendedor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-brand-600 text-white font-bold py-3 rounded-xl hover:bg-brand-700 shadow-lg shadow-brand-500/30 transition-all flex justify-center items-center gap-2">
                    <i class="ph-bold ph-floppy-disk"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // --- LÓGICA DE MODALES ---
    function toggleModal(id) {
        const el = document.getElementById(id);
        if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
            // Animación de entrada simple (opcional)
            el.querySelector('div').classList.remove('scale-95', 'opacity-0');
            el.querySelector('div').classList.add('scale-100', 'opacity-100');
        } else {
            el.classList.add('hidden');
        }
    }

    function editarUsuario(usuario) {
        document.getElementById('edit_id').value = usuario.id_usuario;
        document.getElementById('edit_nombre').value = usuario.nombre;
        document.getElementById('edit_correo').value = usuario.correo;
        document.getElementById('edit_rol').value = usuario.rol;
        toggleModal('modal-editar');
    }

    // --- LÓGICA DE ELIMINACIÓN SEGURA (SweetAlert) ---
    function confirmarEliminacion(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "El usuario perderá el acceso al sistema inmediatamente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Rojo
            cancelButtonColor: '#3085d6', // Azul
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir a la eliminación
                window.location.href = `index.php?controller=usuario&action=eliminar&id=${id}`;
            }
        });
    }

    // --- ALERTAS DE RESPUESTA DEL SERVIDOR ---
    // Leemos la URL para ver si hay mensajes de éxito/error
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const msg = params.get('msg');

        if (msg === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Usuario Creado!',
                text: 'El nuevo colaborador ha sido registrado.',
                timer: 2000, showConfirmButton: false
            });
        } else if (msg === 'updated') {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'Los datos del usuario se han guardado.',
                timer: 2000, showConfirmButton: false
            });
        } else if (msg === 'deleted') { // Necesitas que tu controlador envíe &msg=deleted
            Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: 'El usuario ha sido desactivado.',
                timer: 2000, showConfirmButton: false
            });
        } else if (msg === 'error_email') {
            Swal.fire({
                icon: 'error',
                title: 'Error de Correo',
                text: 'Ese correo electrónico ya está registrado en el sistema.',
                confirmButtonColor: '#0ea5e9'
            });
        }

        // Limpiar URL para que no salga la alerta al recargar
        if (msg) {
            const cleanUrl = window.location.pathname + '?controller=usuario&action=index';
            window.history.replaceState({}, document.title, cleanUrl);
        }
    });
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>