<form
    method="GET"
    class="mb-10 grid gap-3 rounded-md border border-[#eee6e4] bg-white p-4 md:grid-cols-6"
>
    {{-- Busca --}}
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Buscar produto..."
        class="store-input"
    >

    {{-- Preço mínimo --}}
    <input
        type="number"
        name="min_price"
        value="{{ request('min_price') }}"
        placeholder="Preço mínimo"
        class="store-input"
    >

    {{-- Preço máximo --}}
    <input
        type="number"
        name="max_price"
        value="{{ request('max_price') }}"
        placeholder="Preço máximo"
        class="store-input"
    >

    {{-- Cor --}}
    <input
        type="text"
        name="color"
        value="{{ request('color') }}"
        placeholder="Cor"
        class="store-input"
    >

    {{-- Tamanho --}}
    <input
        type="text"
        name="size"
        value="{{ request('size') }}"
        placeholder="Tamanho"
        class="store-input"
    >

    {{-- Botão --}}
    <button
        type="submit"
        class="store-button store-button-primary"
    >
        Filtrar
    </button>
</form>
