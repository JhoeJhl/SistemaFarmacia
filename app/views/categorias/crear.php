<div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Nueva Categoría</h1>

    <form action="/index.php?controller=categoria&action=guardar" method="POST">
        <label>Nombre</label>
        <input type="text" name="nombre" class="w-full border p-2 mb-3" required>

        <label>Descripción</label>
        <textarea name="descripcion" class="w-full border p-2 mb-3"></textarea>

        <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
    </form>
</div>
<?php
$contenido = ob_get_clean();
require __DIR__ . "/../layout/layout.php";
?>