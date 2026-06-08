let variantIndex = {{ isset($product) && $product ? $product->variants->count() : 0 }};

function addVariant() {
    const container = document.getElementById('variants-container');

    const row = document.createElement('div');
    row.classList.add('variant-row', 'grid', 'grid-cols-4', 'gap-2', 'border', 'p-3', 'rounded');

    row.innerHTML = `
        <input type="text" name="variants[${variantIndex}][color]" placeholder="Cor" class="border p-2 rounded">
        <input type="text" name="variants[${variantIndex}][size]" placeholder="Tamanho" class="border p-2 rounded">
        <input type="number" name="variants[${variantIndex}][stock]" placeholder="Estoque" class="border p-2 rounded">
        <button type="button" onclick="this.parentElement.remove()" class="bg-red-600 text-white rounded">X</button>
    `;

    container.appendChild(row);
    variantIndex++;
}
