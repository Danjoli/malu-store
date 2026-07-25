<form method="GET" class="bg-white p-4 rounded shadow mb-8 grid md:grid-cols-6 gap-4">

    {{-- BUSCA --}}
    <input type="text"
           name="search"
           placeholder="Buscar produto..."
           value="{{ request('search') }}"
           class="border rounded p-2">

    {{-- PREÇO MIN --}}
    <input type="number"
           name="min_price"
           placeholder="Preço mínimo"
           value="{{ request('min_price') }}"
           class="border rounded p-2">

    {{-- PREÇO MAX --}}
    <input type="number"
           name="max_price"
           placeholder="Preço máximo"
           value="{{ request('max_price') }}"
           class="border rounded p-2">

    {{-- COR --}}
    <input type="text"
           name="color"
           placeholder="Cor"
           value="{{ request('color') }}"
           class="border rounded p-2">

    {{-- TAMANHO --}}
    <input type="text"
           name="size"
           placeholder="Tamanho"
           value="{{ request('size') }}"
           class="border rounded p-2">

    {{-- BOTÃO --}}
    <button class="bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">
        Filtrar
    </button>

</form>
