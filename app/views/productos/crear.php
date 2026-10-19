<?php ob_start(); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-6xl mx-auto">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Registrar Nuevo Producto</h1>
            <p class="text-gray-500 text-sm">Ingresa los datos del medicamento o insumo.</p>
        </div>
        <a href="index.php?controller=producto&action=index" class="text-gray-500 hover:text-brand-600 flex items-center gap-2 transition-colors">
            <i class="ph ph-arrow-left text-lg"></i> Volver al inventario
        </a>
    </div>

    <form id="form-producto" action="index.php?controller=producto&action=guardar" method="POST" enctype="multipart/form-data">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">

                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Información Básica</h3>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Producto</label>
                        <div class="flex gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo" value="MEDICAMENTO" class="peer sr-only" checked onchange="toggleFields()">
                                <div class="px-4 py-2 rounded-lg border border-gray-200 peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 flex items-center gap-2 hover:bg-gray-50 transition-all">
                                    <i class="ph ph-pill text-xl"></i>
                                    <span class="font-medium">Medicamento</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="tipo" value="GENERAL" class="peer sr-only" onchange="toggleFields()">
                                <div class="px-4 py-2 rounded-lg border border-gray-200 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 flex items-center gap-2 hover:bg-gray-50 transition-all">
                                    <i class="ph ph-package text-xl"></i>
                                    <span class="font-medium">Producto General</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Código de Barras <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="ph ph-barcode absolute left-3 top-3 text-gray-400 text-lg"></i>
                                <input type="text" name="codigo" id="codigo" required class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none" placeholder="Escanea o escribe...">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Comercial <span class="text-red-500">*</span></label>
                            <input type="text" name="nombre" id="nombre" required minlength="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none" placeholder="Ej. Paracetamol 500mg">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría <span class="text-red-500">*</span></label>
                            <select name="categoria_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white outline-none">
                                <option value="">Seleccione...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor <span class="text-red-500">*</span></label>
                            <select name="proveedor_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white outline-none">
                                <option value="">Seleccione...</option>
                                <?php foreach ($proveedores as $prov): ?>
                                    <option value="<?= $prov['id_proveedor'] ?>"><?= $prov['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="pharma-fields" class="bg-blue-50 p-6 rounded-xl shadow-sm border border-blue-100 transition-all duration-300">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="ph ph-prescription text-blue-600 text-xl"></i>
                        <h3 class="font-bold text-blue-900">Datos Farmacéuticos</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-blue-800 mb-1">Fecha Vencimiento <span class="text-red-500">*</span></label>
                            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-blue-800 mb-1">Lote</label>
                            <input type="text" name="lote" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white" placeholder="Ej. L-4050">
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 cursor-pointer pb-2">
                                <input type="checkbox" name="tiene_receta" value="1" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                                <span class="text-sm font-medium text-blue-900">Requiere Receta</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Inventario y Precios</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Compra <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Bs.</span>
                                <input type="number" step="0.01" min="0" name="precio_compra" required class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Venta <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Bs.</span>
                                <input type="number" step="0.01" min="0" name="precio_venta" required class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none font-bold text-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Inicial <span class="text-red-500">*</span></label>
                            <input type="number" name="stock" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none" placeholder="0">
                        </div>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-6">
                    <h3 class="font-bold text-gray-800 mb-4">Imagen del Producto</h3>

                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:bg-gray-50 transition-colors relative group cursor-pointer">
                        <img id="preview" src="#" alt="Vista previa" class="hidden w-full h-48 object-contain mb-4 rounded-lg">

                        <div id="upload-placeholder" class="py-8">
                            <i class="ph ph-image text-4xl text-gray-300 mb-2 group-hover:text-brand-500 transition-colors"></i>
                            <p class="text-sm text-gray-500">Click o arrastra para subir</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG (Max 2MB)</p>
                        </div>

                        <input type="file" name="imagen" id="imagen" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" onchange="mostrarImagen(this)">
                    </div>

                    <div class="mt-6">
                        <button type="button" onclick="validarYGuardar()" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all flex justify-center items-center gap-2">
                            <i class="ph ph-floppy-disk text-xl"></i>
                            Guardar Producto
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    function toggleFields() {
        const tipo = document.querySelector('input[name="tipo"]:checked').value;
        const bloque = document.getElementById('pharma-fields');
        const fechaInput = document.getElementById('fecha_vencimiento');

        if (tipo === 'MEDICAMENTO') {
            bloque.classList.remove('hidden');
            bloque.classList.add('block');
            fechaInput.setAttribute('required', 'required'); // Hacer obligatorio si es medicamento
        } else {
            bloque.classList.remove('block');
            bloque.classList.add('hidden');
            fechaInput.removeAttribute('required'); // Quitar obligatorio
            fechaInput.value = ''; // Limpiar valor
        }
    }

    function mostrarImagen(input) {
        // Validación de tamaño (2MB)
        if (input.files && input.files[0]) {
            if (input.files[0].size > 2 * 1024 * 1024) {
                Swal.fire('Error', 'La imagen es muy pesada. Máximo 2MB.', 'error');
                input.value = ''; // Limpiar input
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('preview').classList.remove('hidden');
                document.getElementById('upload-placeholder').classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function validarYGuardar() {
        const form = document.getElementById('form-producto');

        // Verificar validez HTML5 (campos required)
        if (!form.checkValidity()) {
            form.reportValidity(); // Muestra los mensajes nativos del navegador
            return;
        }

        // Validaciones extra de negocio
        const precioCompra = parseFloat(document.querySelector('input[name="precio_compra"]').value);
        const precioVenta = parseFloat(document.querySelector('input[name="precio_venta"]').value);

        if (precioVenta < precioCompra) {
            Swal.fire({
                icon: 'warning',
                title: 'Cuidado',
                text: 'El precio de venta es MENOR al precio de compra. ¿Deseas continuar?',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Corregir'
            }).then((result) => {
                if (result.isConfirmed) enviarFormulario();
            });
            return;
        }

        // Si todo ok, enviar
        enviarFormulario();
    }

    function enviarFormulario() {
        const form = document.getElementById('form-producto');

        // Mostrar alerta de carga
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        form.submit();
    }

    // Inicializar
    toggleFields();
</script>

<?php
// Lógica para mostrar alerta de éxito al volver (si el controlador redirecciona con ?msg=creado)
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: '¡Producto Creado!',
                text: 'El producto se ha registrado correctamente en el inventario.',
                confirmButtonColor: '#0ea5e9'
            });
            // Limpiar URL
            window.history.replaceState({}, document.title, window.location.pathname + '?controller=producto&action=index');
        });
    </script>";
}
if (isset($_GET['status']) && $_GET['status'] == 'error_validacion') {
    // Recuperamos el mensaje y decodificamos caracteres especiales
    $errorMsg = isset($_GET['msg']) ? urldecode($_GET['msg']) : 'Datos incorrectos';

    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error de Validación',
                text: '" . $errorMsg . "',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Corregir'
            });
            // Limpiar la URL para que no salga la alerta al recargar
            window.history.replaceState({}, document.title, window.location.pathname + '?controller=producto&action=crear');
        });
    </script>";
}

$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';

$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';

?>