<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Editar Categoría</h1>

    <form action="/index.php?controller=categoria&action=actualizar" method="POST">
        <input type="hidden" name="id" value="<?= $dato['id_categoria'] ?>">

        <label>Nombre</label>
        <input type="text" name="nombre" value="<?= $dato['nombre'] ?>" class="w-full border p-2 mb-3">

        <label>Descripción</label>
        <textarea name="descripcion" class="w-full border p-2 mb-3"><?= $dato['descripcion'] ?></textarea>

        <button class="px-4 py-2 bg-blue-600 text-white rounded">Actualizar</button>
    </form>
</div>
<?php
$contenido = ob_get_clean();
require __DIR__ . "/../layout/layout.php";
?>