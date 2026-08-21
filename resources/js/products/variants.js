let variantIndex = window.PRODUCT?.variantIndex ?? 0;

function addVariant() {
    const container = document.getElementById('variants-container');

    if (!container) {
        return;
    }

    const row = document.createElement('div');

    row.classList.add(
        'variant-row',
        'grid',
        'grid-cols-4',
        'gap-2',
        'border',
        'p-3',
        'rounded'
    );

    const sizeOptions = (window.CLOTHING_SIZES ?? [])
        .map((size) => `<option value="${size}">${size}</option>`)
        .join('');

    row.innerHTML = `
        <input type="text" name="variants[${variantIndex}][color]" placeholder="Cor" class="border p-2 rounded">
        <select name="variants[${variantIndex}][size]" class="border p-2 rounded">${sizeOptions}</select>
        <input type="number" name="variants[${variantIndex}][stock]" placeholder="Estoque" class="border p-2 rounded">
        <button type="button" class="remove-variant bg-red-600 text-white rounded px-2">X</button>
    `;

    row.querySelector('.remove-variant').addEventListener('click', () => {
        row.remove();
    });

    container.appendChild(row);

    variantIndex++;
}

document.addEventListener('DOMContentLoaded', () => {
    document
        .getElementById('btn-add-variant')
        ?.addEventListener('click', addVariant);
});
