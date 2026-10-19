// Devuelve la foto del producto o, si no tiene, un icono según su tipo
function imagenProductoHtml(p, clasesImg = 'w-full h-full object-contain', tamIcono = 'text-6xl') {
    if (p.imagen && p.imagen !== 'default_product.png') {
        return `<img src="img/uploads/productos/${encodeURIComponent(p.imagen)}" class="${clasesImg}">`;
    }
    const icono = p.tipo === 'MEDICAMENTO' ? 'ph-pill' : 'ph-package';
    return `<div class="w-full h-full flex items-center justify-center text-sky-300"><i class="ph-duotone ${icono} ${tamIcono}"></i></div>`;
}
