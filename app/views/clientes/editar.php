<h2>Editar Cliente</h2>

<form action="index.php?controller=cliente&action=update" method="POST">
    <input type="hidden" name="id" value="<?= $cliente['id_cliente'] ?>">

    Nombre: <input type="text" name="nombre" value="<?= $cliente['nombre'] ?>" required><br><br>
    Teléfono: <input type="text" name="telefono" value="<?= $cliente['telefono'] ?>" required><br><br>
    Email: <input type="email" name="correo" value="<?= $cliente['correo'] ?>" required><br><br>

    <button type="submit">Actualizar</button>
</form>
<?php
$contenido = ob_get_clean();
require __DIR__ . "/../layout/layout.php";
?>