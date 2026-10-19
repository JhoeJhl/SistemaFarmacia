<h2 class="text-2xl font-bold mb-4">Nuevo Proveedor</h2>

<form action="?controller=proveedores&action=store" method="POST">

    <label>Nombre:</label>
    <input type="text" name="nombre" class="border p-2 w-full mb-3" required>

    <label>Teléfono:</label>
    <input type="text" name="telefono" class="border p-2 w-full mb-3" required>

    <label>Dirección:</label>
    <input type="text" name="direccion" class="border p-2 w-full mb-3" required>

    <button class="bg-green-600 text-white px-4 py-2 rounded" type="submit">
        Guardar
    </button>
</form>
<?php
$contenido = ob_get_clean();
require __DIR__ . "/../layout/layout.php";
?>