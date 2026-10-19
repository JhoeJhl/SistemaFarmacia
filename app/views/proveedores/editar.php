<h2 class="text-2xl font-bold mb-4">Editar Proveedor</h2>

<form action="?controller=proveedores&action=update" method="POST">

       <input type="hidden" name="id" value="<?= $proveedor['id_proveedor'] ?>">

       <label>Nombre:</label>
       <input type="text" name="nombre" value="<?= $proveedor['nombre'] ?>"
              class="border p-2 w-full mb-3" required>

       <label>Teléfono:</label>
       <input type="text" name="telefono" value="<?= $proveedor['telefono'] ?>"
              class="border p-2 w-full mb-3" required>

       <label>Dirección:</label>
       <input type="text" name="direccion" value="<?= $proveedor['direccion'] ?>"
              class="border p-2 w-full mb-3" required>

       <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">
              Actualizar
       </button>
</form>
<?php
$contenido = ob_get_clean();
require __DIR__ . "/../layout/layout.php";
?>