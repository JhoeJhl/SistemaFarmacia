<?php ob_start(); ?>

<div class="max-w-6xl mx-auto">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Producto</h1>
            <p class="text-gray-500 text-sm">Modificando: <span class="font-bold text-brand-600"><?= $producto['nombre'] ?></span></p>
        </div>
        <a href="index.php?controller=producto&action=index" class="text-gray-500 hover:text-brand-600 flex items-center gap-2 transition-colors">
            <i class="ph ph-arrow-left text-lg"></i> Cancelar y Volver
        </a>
    </div>

    <form action="index.php?controller=producto&action=actualizar" method="POST" enctype="multipart/form-data">
        
        <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Información Básica</h3>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Producto</label>
                        <div class="flex gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo" value="MEDICAMENTO" class="peer sr-only" 
                                       <?= $producto['tipo'] == 'MEDICAMENTO' ? 'checked' : '' ?> 
                                       onchange="toggleFields()">
                                <div class="px-4 py-2 rounded-lg border border-gray-200 peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 flex items-center gap-2 hover:bg-gray-50 transition-all">
                                    <i class="ph ph-pill text-xl"></i>
                                    <span class="font-medium">Medicamento</span>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo" value="GENERAL" class="peer sr-only" 
                                       <?= $producto['tipo'] == 'GENERAL' ? 'checked' : '' ?> 
                                       onchange="toggleFields()">
                                <div class="px-4 py-2 rounded-lg border border-gray-200 peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 flex items-center gap-2 hover:bg-gray-50 transition-all">
                                    <i class="ph ph-package text-xl"></i>
                                    <span class="font-medium">Producto General</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Código de Barras</label>
                            <input type="text" name="codigo" value="<?= $producto['codigo_barras'] ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Comercial</label>
                            <input type="text" name="nombre" value="<?= $producto['nombre'] ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none"><?= $producto['descripcion'] ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <select name="categoria_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white outline-none">
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" <?= $cat['id_categoria'] == $producto['id_categoria'] ? 'selected' : '' ?>>
                                        <?= $cat['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                            <select name="proveedor_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white outline-none">
                                <?php foreach($proveedores as $prov): ?>
                                    <option value="<?= $prov['id_proveedor'] ?>" <?= $prov['id_proveedor'] == $producto['id_proveedor'] ? 'selected' : '' ?>>
                                        <?= $prov['nombre'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="pharma-fields" class="bg-blue-50 p-6 rounded-xl shadow-sm border border-blue-100 transition-all duration-300 <?= $producto['tipo'] != 'MEDICAMENTO' ? 'hidden' : '' ?>">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="ph ph-prescription text-blue-600 text-xl"></i>
                        <h3 class="font-bold text-blue-900">Datos Farmacéuticos</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-blue-800 mb-1">Fecha Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" value="<?= $producto['fecha_vencimiento'] ?>" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-blue-800 mb-1">Lote</label>
                            <input type="text" name="lote" value="<?= $producto['lote'] ?? '' ?>" class="w-full px-4 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Inventario y Precios</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Compra</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Bs.</span>
                                <input type="number" step="0.01" name="precio_compra" value="<?= $producto['precio_compra'] ?>" required class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio Venta</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Bs.</span>
                                <input type="number" step="0.01" name="precio_venta" value="<?= $producto['precio_venta'] ?>" required class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none font-bold text-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Actual</label>
                            <input type="number" name="stock" value="<?= $producto['stock'] ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 outline-none">
                        </div>
                    </div>
                </div>

            </div>

            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-6">
                    <h3 class="font-bold text-gray-800 mb-4">Imagen del Producto</h3>
                    
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:bg-gray-50 transition-colors relative">
                        <?php $conFoto = tieneImagenProducto($producto['imagen'] ?? ''); ?>
                        <img id="preview" 
                             src="<?= $conFoto ? 'img/uploads/productos/' . rawurlencode($producto['imagen']) : '' ?>" 
                             class="w-full h-48 object-contain mb-4 rounded-lg <?= $conFoto ? '' : 'hidden' ?>">
                        
                        <div id="upload-placeholder" class="h-48 mb-4 flex flex-col items-center justify-center <?= $conFoto ? 'hidden' : '' ?>">
                            <i class="ph-duotone ph-image text-6xl text-sky-300 mb-2"></i>
                            <p class="text-sm text-gray-500">Sin imagen. Haz clic para subir una</p>
                        </div>
                        
                        <input type="file" name="imagen" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" onchange="mostrarImagen(this)">
                    </div>
                    
                    <p class="text-xs text-gray-400 text-center mt-2">Sube una nueva solo si deseas reemplazar la actual.</p>

                    <div class="mt-6">
                        <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all flex justify-center items-center gap-2">
                            <i class="ph ph-floppy-disk text-xl"></i>
                            Guardar Cambios
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
        
        if (tipo === 'MEDICAMENTO') {
            bloque.classList.remove('hidden');
        } else {
            bloque.classList.add('hidden');
        }
    }

    function mostrarImagen(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                document.getElementById('upload-placeholder').classList.add('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Ejecutar al inicio para asegurar el estado correcto
    toggleFields();
</script>

<?php 
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php'; 
?>