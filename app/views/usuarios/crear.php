<?php require __DIR__ . "/../layout.php"; ?>
<h2 class="text-2xl font-bold mb-4">Nuevo Usuario</h2>

<form action="?controller=usuarios&action=store" method="POST">

    <label>Nombre:</label>
    <input type="text" name="nombre" class="border p-2 w-full mb-3" required>

    <label>Email:</label>
    <input type="email" name="email" class="border p-2 w-full mb-3" required>

    <label>Contraseña:</label>
    <input type="password" name="password" class="border p-2 w-full mb-3" required>

    <label>Rol:</label>
    <select name="rol" class="border p-2 w-full mb-3">
        <option value="admin">Admin</option>
        <option value="empleado">Empleado</option>
    </select>

    <button class="bg-green-600 text-white px-4 py-2 rounded" type="submit">
        Guardar
    </button>

</form>
