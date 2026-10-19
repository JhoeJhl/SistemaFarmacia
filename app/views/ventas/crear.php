<?php ob_start(); ?>

<div class="flex flex-col lg:flex-row gap-5 lg:h-[calc(100vh-7.25rem)] lg:-mb-14">

    <!-- PANEL DE PRODUCTOS -->
    <section class="flex-1 flex flex-col bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden min-h-[60vh] lg:min-h-0">

        <div class="p-4 border-b border-gray-100 space-y-3">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                        <i class="ph-duotone ph-storefront text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-800 leading-tight">Punto de Venta</h1>
                        <p class="text-xs text-gray-500"><span id="contador-productos">0</span> productos disponibles</p>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-3 text-[11px] text-gray-400">
                    <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-200 rounded font-mono text-gray-600">F2</kbd> Buscar</span>
                    <span class="flex items-center gap-1"><kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-200 rounded font-mono text-gray-600">F9</kbd> Cobrar</span>
                </div>
            </div>

            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
                <input type="text" id="buscador" autocomplete="off" placeholder="Buscar por nombre o escanear código de barras..."
                    class="w-full pl-11 pr-11 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition text-sm">
                <i class="ph ph-barcode absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-2xl" title="Compatible con lector de código de barras"></i>
            </div>

            <div class="flex gap-2 overflow-x-auto no-scrollbar" id="filtros">
                <button data-filtro="all" class="filtro-btn flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-medium border transition whitespace-nowrap">
                    <i class="ph ph-squares-four"></i> Todos
                </button>
                <button data-filtro="MEDICAMENTO" class="filtro-btn flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-medium border transition whitespace-nowrap">
                    <i class="ph ph-pill"></i> Medicamentos
                </button>
                <button data-filtro="GENERAL" class="filtro-btn flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-medium border transition whitespace-nowrap">
                    <i class="ph ph-package"></i> Generales
                </button>
                <button data-filtro="STOCK" class="filtro-btn flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-sm font-medium border transition whitespace-nowrap">
                    <i class="ph ph-check-circle"></i> Con stock
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-4 bg-gray-50/60">
            <div id="grid-productos" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3"></div>
        </div>
    </section>

    <!-- PANEL DE LA VENTA -->
    <aside class="w-full lg:w-[380px] xl:w-[420px] flex flex-col bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden shrink-0">

        <div class="p-4 bg-gradient-to-br from-sky-900 to-sky-950 text-white shrink-0">
            <div class="flex items-center justify-between">
                <h2 class="font-bold flex items-center gap-2">
                    <i class="ph-duotone ph-receipt text-2xl text-sky-300"></i> Venta actual
                </h2>
                <button onclick="vaciarCarrito()" id="btn-vaciar" class="text-xs flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-sky-200 hover:text-white hover:bg-white/10 transition disabled:opacity-40 disabled:pointer-events-none" disabled>
                    <i class="ph ph-trash text-base"></i> Vaciar
                </button>
            </div>

            <label class="text-[10px] text-sky-300 uppercase font-bold tracking-wider mt-4 mb-1 block">Cliente</label>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <i class="ph ph-user absolute left-3 top-1/2 -translate-y-1/2 text-sky-300 text-lg pointer-events-none"></i>
                    <select id="select-cliente" class="w-full appearance-none bg-white/10 border border-white/15 text-white rounded-lg pl-10 pr-9 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400">
                        <?php if (isset($clientes)): foreach ($clientes as $c): ?>
                            <option class="text-gray-800" value="<?= (int)$c['id_cliente'] ?>"><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; endif; ?>
                    </select>
                    <i class="ph ph-caret-down absolute right-3 top-1/2 -translate-y-1/2 text-sky-300 pointer-events-none"></i>
                </div>
                <a href="index.php?controller=cliente&action=index" target="_blank" class="w-11 flex items-center justify-center bg-white/10 hover:bg-white/20 border border-white/15 rounded-lg transition" title="Registrar cliente">
                    <i class="ph ph-user-plus text-lg"></i>
                </a>
            </div>
        </div>

        <div id="cart-body" class="flex-1 overflow-y-auto divide-y divide-gray-100 min-h-[200px]"></div>

        <div class="p-4 bg-gray-50 border-t border-gray-200 shrink-0 space-y-3">
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between text-gray-500">
                    <span class="flex items-center gap-1.5"><i class="ph ph-shopping-bag"></i> Artículos</span>
                    <span id="lbl-articulos" class="font-medium text-gray-700">0</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span class="flex items-center gap-1.5"><i class="ph ph-calculator"></i> Subtotal</span>
                    <span id="lbl-subtotal" class="font-medium text-gray-700">Bs. 0.00</span>
                </div>
            </div>
            <div class="flex justify-between items-end pt-3 border-t border-dashed border-gray-300">
                <span class="text-sm font-bold text-gray-600 uppercase tracking-wide">Total</span>
                <span class="text-3xl font-extrabold text-sky-900" id="lbl-total">Bs. 0.00</span>
            </div>

            <form id="form-venta" action="index.php?controller=venta&action=guardar" method="POST">
                <input type="hidden" name="cliente_id" id="input-cliente">
                <input type="hidden" name="productos_json" id="input-json">

                <button type="button" id="btn-cobrar" onclick="abrirCobro()" disabled
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex justify-center items-center gap-2 text-lg active:scale-[0.98] disabled:bg-gray-300 disabled:shadow-none disabled:cursor-not-allowed">
                    <i class="ph ph-cash-register text-2xl"></i>
                    Cobrar
                    <kbd class="hidden md:inline text-[10px] font-mono bg-white/20 px-1.5 py-0.5 rounded ml-1">F9</kbd>
                </button>
            </form>
        </div>
    </aside>
</div>

<script>
    const productosDB = <?= json_encode($productos) ?>;
</script>
<script src="js/producto-imagen.js"></script>
<script src="js/pos.js"></script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layout/layout.php';
?>
