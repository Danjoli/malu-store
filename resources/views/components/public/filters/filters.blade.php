<form method="GET" class="mb-10 grid gap-3 rounded-md border border-[#eee6e4] bg-white p-4 md:grid-cols-6">

    {{-- BUSCA --}}
    <input type="text" name="search" placeholder="Buscar produto..." value="{{ request('search') }}" class="store-input">

    {{-- PREÇO MIN --}}
    <input type="number" name="min_price" placeholder="Preço mínimo" value="{{ request('min_price') }}"
        class="store-input">

    {{-- PREÇO MAX --}}
    <input type="number" name="max_price" placeholder="Preço máximo" value="{{ request('max_price') }}"
        class="store-input">

    {{-- COR --}}
    <input type="text" name="color" placeholder="Cor" value="{{ request('color') }}" class="store-input">

    {{-- TAMANHO --}}
    <input type="text" name="size" placeholder="Tamanho" value="{{ request('size') }}" class="store-input">

    {{-- BOTÃO --}}
    <button class="store-button store-button-primary">
        Filtrar
    </button>

</form>
