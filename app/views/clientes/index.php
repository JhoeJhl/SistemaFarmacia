<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Clientes</h1>

    <a href="index.php?controller=cliente&action=crear"
        class="bg-blue-600 text-white px-4 py-2 rounded">Cliente</a>

    <table class="w-full mt-4 border">
        <thead class="bg-gray-100">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td><?= $c['id_cliente'] ?></td>
                    <td><?= $c['nombre'] ?></td>
                    <td><?= $c['telefono'] ?></td>
                    <td><?= $c['correo'] ?></td>
                    <td>
                        <a href="?controller=cliente&action=edit&id=<?= $c['id_cliente'] ?>">Editar</a> |
                        <a href="?controller=cliente&action=delete&id=<?= $c['id_cliente'] ?>">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$contenido = ob_get_clean();
require __DIR__ . "/../layout/layout.php";
?>