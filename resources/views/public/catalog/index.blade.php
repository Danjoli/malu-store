@extends('layouts.public.app')

@section('title', 'Todos os produtos')

@section('content')
    @php
        $selectedColors = array_values(array_filter((array) request('color')));
        $selectedSizes = array_values(array_filter((array) request('size')));
        $activeFilterCount = count($selectedColors) + count($selectedSizes)
            + (request()->filled('category') ? 1 : 0)
            + (request()->filled('min_price') ? 1 : 0)
            + (request()->filled('max_price') ? 1 : 0);
        $baseParameters = request()->except(['page', 'category']);
    @endphp

    <div data-catalog-shell class="store-container py-8 transition-opacity md:py-10" aria-live="polite">
        <nav class="store-muted mb-7 text-xs" aria-label="Navegação estrutural">
            <a href="{{ route('home') }}" class="hover:underline">Home</a>
            <span class="mx-1">›</span>
            <span aria-current="page">Produtos</span>
        </nav>

        <form action="{{ route('catalog.index') }}" method="GET" data-catalog-form>
            <div class="mb-6 grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                <label class="relative block">
                    <span class="sr-only">Buscar no catálogo</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou descrição..." class="store-input w-full pl-10" autocomplete="off" data-catalog-live-input>
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-stone-400" aria-hidden="true">⌕</span>
                </label>
                <div class="flex gap-2">
                    <button type="button" data-catalog-filter-toggle aria-expanded="false" class="store-button store-button-outline flex-1 lg:hidden">
                        Filtros{{ $activeFilterCount ? ' ('.$activeFilterCount.')' : '' }}
                    </button>
                    <button class="store-button store-button-primary px-6">Buscar</button>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-[220px_1fr]">
                <aside data-catalog-filters class="store-card hidden self-start p-5 lg:block">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="store-kicker">Filtros</h2>
                        @if ($activeFilterCount > 0 || request()->filled('search') || request('sort'))
                            <a href="{{ route('catalog.index') }}" data-catalog-link class="text-[11px] font-semibold text-[var(--store-accent-strong)] hover:underline">Limpar tudo</a>
                        @endif
                    </div>

                    <div class="mt-5">
                        <h3 class="mb-2 text-xs font-bold text-stone-800">Categorias</h3>
                        <nav class="space-y-1 text-xs text-stone-600">
                            <a href="{{ route('catalog.index', $baseParameters) }}" data-catalog-link class="block rounded-lg px-3 py-2 {{ !request('category') ? 'bg-[var(--store-surface-accent)] text-stone-900' : 'hover:bg-[var(--store-surface-soft)]' }}">Todos</a>
                            @foreach ($categories as $category)
                                <a href="{{ route('catalog.index', [...$baseParameters, 'category' => $category->slug]) }}" data-catalog-link class="block rounded-lg px-3 py-2 {{ request('category') === $category->slug ? 'bg-[var(--store-surface-accent)] text-stone-900' : 'hover:bg-[var(--store-surface-soft)]' }}">{{ $category->name }}</a>
                            @endforeach
                        </nav>
                        @if (request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                    </div>

                    @if ($filterColors->isNotEmpty())
                        <fieldset class="mt-6 border-t border-[var(--store-border)] pt-5">
                            <legend class="mb-3 text-xs font-bold text-stone-800">Cor</legend>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($filterColors as $color)
                                    <label class="cursor-pointer" title="{{ $color }}">
                                        <input type="checkbox" name="color[]" value="{{ $color }}" class="peer sr-only" @checked(in_array($color, $selectedColors, true))>
                                        <span class="flex min-h-9 items-center gap-2 rounded-full border border-[var(--store-border)] px-2.5 py-1.5 text-[10px] font-semibold peer-checked:border-[var(--store-accent)] peer-checked:bg-[var(--store-surface-accent)] peer-checked:ring-1 peer-checked:ring-[var(--store-accent)]">
                                            <span class="h-4 w-4 rounded-full border border-black/15" style="background-color: {{ \App\Support\ProductColor::css($color) }}"></span>{{ $color }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endif

                    @if ($filterSizes->isNotEmpty())
                        <fieldset class="mt-6">
                            <legend class="mb-3 text-xs font-bold text-stone-800">Tamanho</legend>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($filterSizes as $size)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="size[]" value="{{ $size }}" class="peer sr-only" @checked(in_array($size, $selectedSizes, true))>
                                        <span class="flex h-9 min-w-10 items-center justify-center rounded-md border border-[var(--store-border)] px-2 text-xs font-bold peer-checked:border-[var(--store-accent)] peer-checked:bg-[var(--store-accent)] peer-checked:text-[var(--store-accent-contrast)]">{{ $size }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                    @endif

                    <fieldset class="mt-6">
                        <legend class="mb-3 text-xs font-bold text-stone-800">Faixa de preço</legend>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" min="0" step="1" name="min_price" value="{{ request('min_price') }}" placeholder="Mínimo" class="store-input px-2 py-2 text-xs" data-catalog-live-input>
                            <input type="number" min="0" step="1" name="max_price" value="{{ request('max_price') }}" placeholder="Máximo" class="store-input px-2 py-2 text-xs" data-catalog-live-input>
                        </div>
                    </fieldset>
                </aside>

                <section>
                    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p class="store-kicker mb-1">Curadoria completa</p>
                            <h1 data-catalog-heading tabindex="-1" class="store-title text-3xl outline-none md:text-4xl">{{ request('search') ? 'Resultados para “'.request('search').'”' : 'Todos os produtos' }}</h1>
                            <p class="mt-2 text-xs text-stone-500">Página {{ $products->currentPage() }}{{ $activeFilterCount ? ' · '.$activeFilterCount.' filtros ativos' : '' }}</p>
                        </div>
                        <label class="flex items-center gap-2">
                            <span class="hidden text-[10px] text-stone-500 sm:inline">Ordenar por:</span>
                            <select name="sort" class="store-input w-auto py-2 text-xs">
                                <option value="recent" @selected(request('sort', 'recent') === 'recent')>Mais recentes</option>
                                <option value="price_asc" @selected(request('sort') === 'price_asc')>Menor preço</option>
                                <option value="price_desc" @selected(request('sort') === 'price_desc')>Maior preço</option>
                            </select>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 md:gap-5">
                        @forelse ($products as $product)
                            <x-store.product-card :product="$product" />
                        @empty
                            <div class="col-span-full rounded-xl border border-dashed border-[var(--store-border)] py-16 text-center">
                                <p class="font-semibold text-stone-700">Nenhum produto encontrado.</p>
                                <p class="mt-2 text-xs text-stone-500">Tente remover algum filtro ou buscar outro termo.</p>
                                <a href="{{ route('catalog.index') }}" data-catalog-link class="store-button store-button-outline mt-5 inline-flex">Limpar filtros</a>
                            </div>
                        @endforelse
                    </div>

                    @if ($products->hasPages())
                        <nav class="mt-10 flex items-center justify-center gap-3 text-xs" aria-label="Paginação">
                            @if ($products->onFirstPage())
                                <span class="px-3 text-stone-300" aria-disabled="true">‹ Anterior</span>
                            @else
                                <a href="{{ $products->previousPageUrl() }}" data-catalog-link class="px-3 text-stone-700 hover:text-[var(--store-accent-strong)]">‹ Anterior</a>
                            @endif
                            <span class="flex h-8 items-center justify-center rounded-full bg-[var(--store-accent)] px-4 font-semibold text-[var(--store-accent-contrast)]" aria-current="page">Página {{ $products->currentPage() }}</span>
                            @if ($products->hasMorePages())
                                <a href="{{ $products->nextPageUrl() }}" data-catalog-link class="px-3 text-stone-700 hover:text-[var(--store-accent-strong)]">Próxima ›</a>
                            @else
                                <span class="px-3 text-stone-300" aria-disabled="true">Próxima ›</span>
                            @endif
                        </nav>
                    @endif
                </section>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/catalog/index.js')
@endpush
