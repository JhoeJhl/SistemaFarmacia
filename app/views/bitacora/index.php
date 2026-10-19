<?php ob_start(); ?>

<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <i class="ph ph-shield-check text-brand-600"></i> Seguimiento de Movimientos
    </h1>
    <p class="text-xs text-gray-400 mt-4 text-center">
        <i class="ph ph-info"></i>
        Por seguridad y rendimiento, el sistema elimina automáticamente los registros con más de 60 días de antigüedad.
    </p>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-800 text-gray-200 uppercase font-medium text-xs">
                <tr>
                    <th class="p-4">Fecha/Hora</th>
                    <th class="p-4">Usuario</th>
                    <th class="p-4">Acción</th>
                    <th class="p-4">Tabla</th>
                    <th class="p-4">Detalle</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-gray-50 font-mono text-xs">
                        <td class="p-3 text-gray-500 whitespace-nowrap"><?= date('d/m/Y H:i:s', strtotime($log['fecha'])) ?></td>
                        <td class="p-3 font-bold text-brand-700"><?= $log['usuario'] ?></td>
                        <td class="p-3">
                            <?php
                            $color = 'text-gray-600';
                            if ($log['accion'] == 'ELIMINAR') $color = 'text-red-600 font-bold bg-red-50 px-2 py-1 rounded';
                            if ($log['accion'] == 'LOGIN') $color = 'text-green-600 font-bold bg-green-50 px-2 py-1 rounded';
                            if ($log['accion'] == 'CREAR') $color = 'text-blue-600 font-bold';
                            ?>
                            <span class="<?= $color ?>"><?= $log['accion'] ?></span>
                        </td>
                        <td class="p-3 text-gray-500 uppercase"><?= $log['tabla_afectada'] ?></td>
                        <td class="p-3 text-gray-700"><?= $log['detalle'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>