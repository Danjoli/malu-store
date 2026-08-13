@extends('layouts.public.app')

@section('title', 'Todos os produtos')

@section('content')
<div class="store-container py-8 md:py-10">
    <div class="mb-7 text-xs text-stone-500">Home <span class="mx-1">›</span> Produtos</div>
    <div class="grid gap-8 lg:grid-cols-[190px_1fr]">
        <aside class="hidden lg:block">
            <h2 class="mb-3 text-xs font-bold text-stone-800">Categorias</h2>
            <nav class="space-y-1 text-xs text-stone-600">
                <a href="{{ route('catalog.index') }}" class="block rounded-sm px-3 py-2 {{ !request('category') ? 'bg-[#fff1ef] text-stone-900' : 'hover:bg-[#fff8f7]' }}">Todos</a>
                @foreach($categories as $category)<a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="block rounded-sm px-3 py-2 {{ request('category') === $category->slug ? 'bg-[#fff1ef] text-stone-900' : 'hover:bg-[#fff8f7]' }}">{{ $category->name }}</a>@endforeach
            </nav>
            <form method="GET" class="mt-8 border-t border-[#eee6e4] pt-6 text-xs">
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <h2 class="mb-4 font-bold text-stone-800">Filtros</h2>
                <p class="mb-3 font-semibold text-stone-700">Cor</p>
                <div class="mb-6 flex gap-2.5">
                    @foreach(['Off-white' => '#f6eee4', 'Azul' => '#1b56aa', 'Preto' => '#1d1d1d', 'Cinza' => '#a9a9a9', 'Rosé' => '#d77b8c'] as $color => $hex)
                        <label class="cursor-pointer"><input type="radio" class="peer sr-only" name="color" value="{{ $color }}" @checked(request('color') === $color)><span title="{{ $color }}" class="block h-4 w-4 rounded-full border border-stone-300 ring-offset-2 peer-checked:ring-2 peer-checked:ring-[#bd5564]" style="background-color: {{ $hex }}"></span></label>
                    @endforeach
                </div>
                <p class="mb-2 font-semibold text-stone-700">Tamanho</p>
                <div class="space-y-2 text-stone-600">@foreach(['PP','P','M','G','GG'] as $size)<label class="flex items-center gap-2"><input type="checkbox" name="size" value="{{ $size }}" @checked(request('size') === $size)> {{ $size }}</label>@endforeach</div>
                <p class="mb-2 mt-6 font-semibold text-stone-700">Faixa de preço</p>
                <div class="relative mb-4 h-4"><div class="absolute top-1/2 h-0.5 w-full -translate-y-1/2 bg-stone-800"></div><span class="absolute left-0 top-1/2 h-3 w-3 -translate-y-1/2 rounded-full border-2 border-stone-800 bg-white"></span><span class="absolute right-0 top-1/2 h-3 w-3 -translate-y-1/2 rounded-full border-2 border-stone-800 bg-white"></span></div>
                <div class="grid grid-cols-2 gap-2"><input class="store-input px-2 py-2 text-xs" name="min_price" placeholder="R$ 0" value="{{ request('min_price') }}"><input class="store-input px-2 py-2 text-xs" name="max_price" placeholder="R$ 300+" value="{{ request('max_price') }}"></div>
                <button class="store-button store-button-primary mt-4 w-full">Filtrar</button>
            </form>
        </aside>
        <section>
            <div class="mb-6 flex items-center justify-between"><h1 class="store-title text-3xl md:text-4xl">Todos os produtos</h1><form method="GET" class="flex items-center gap-2"><span class="hidden text-[10px] text-stone-500 sm:inline">Ordenar por:</span><select name="sort" onchange="this.form.submit()" class="store-input w-auto py-2 text-xs"><option value="recent" @selected(request('sort','recent') === 'recent')>Mais recentes</option><option value="price_asc" @selected(request('sort') === 'price_asc')>Menor preço</option><option value="price_desc" @selected(request('sort') === 'price_desc')>Maior preço</option></select></form></div>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 md:gap-5">@forelse($products as $product)<x-store.product-card :product="$product" />@empty<div class="col-span-full py-16 text-center text-stone-500">Nenhum produto encontrado.</div>@endforelse</div>
            @if($products->hasPages())
                <nav class="mt-10 flex items-center justify-center gap-1.5 text-xs" aria-label="Paginação">
                    @if($products->onFirstPage())<span class="px-2 text-stone-300">‹</span>@else<a href="{{ $products->previousPageUrl() }}" class="px-2 text-stone-700 hover:text-[#bd5564]">‹</a>@endif
                    @for($page = 1; $page <= $products->lastPage(); $page++)
                        <a href="{{ $products->url($page) }}" class="flex h-7 min-w-7 items-center justify-center rounded-sm px-1 {{ $page === $products->currentPage() ? 'bg-[#d66f7c] text-white' : 'text-stone-700 hover:bg-[#fff1ef]' }}">{{ $page }}</a>
                    @endfor
                    @if($products->hasMorePages())<a href="{{ $products->nextPageUrl() }}" class="px-2 text-stone-700 hover:text-[#bd5564]">›</a>@else<span class="px-2 text-stone-300">›</span>@endif
                </nav>
            @endif
        </section>
    </div>
</div>
@endsection
