@extends('layouts.public.app')

@section('title', 'Loja')

@section('content')
    {{-- Banner principal --}}
    <section class="store-divider border-b bg-[var(--store-surface-soft)]">
        <div class="relative min-h-[520px] overflow-hidden sm:min-h-[500px] md:min-h-[580px]">
            <img
                src="{{ app(\App\Support\ProductImageStorage::class)->url('hero-malu-store.png') }}"
                alt="Nova coleção Malu Store"
                fetchpriority="high"
                class="absolute inset-0 h-full w-full scale-[1.01] object-cover object-[64%_center] sm:object-[65%_center]"
            >

            <div class="absolute inset-0 bg-[linear-gradient(90deg,var(--store-surface-soft)_0%,color-mix(in_srgb,var(--store-surface-soft)_91%,transparent)_38%,transparent_78%)]"></div>
            <div class="absolute -left-24 top-16 h-72 w-72 rounded-full border border-[var(--store-accent)]/20"></div>
            <div class="absolute left-10 top-28 h-52 w-52 rounded-full border border-[var(--store-accent)]/15"></div>

            <div class="store-container home-container relative z-10 flex min-h-[520px] items-center py-16 sm:min-h-[500px] md:min-h-[580px]">
                <div class="max-w-[20rem] sm:max-w-xl">
                    <p class="store-kicker mb-6 flex items-center gap-3">
                        <span class="h-px w-8 bg-[var(--store-accent)]"></span>
                        Edição Primavera
                    </p>

                    <h1 class="leading-[.82] text-stone-900">
                        <span class="hero-display block text-[3.25rem] sm:text-6xl md:text-[5rem]">Vista o que faz você</span>
                        <em class="hero-script -mt-1 block text-[4.5rem] leading-[.75] text-[var(--store-accent)] sm:text-[6.5rem] md:text-[8rem]">florescer</em>
                    </h1>

                    <p class="store-muted mt-7 max-w-sm text-sm font-medium leading-6 sm:text-base">
                        Peças femininas escolhidas para acompanhar dias comuns e momentos inesquecíveis com leveza.
                    </p>

                    <a href="#produtos" class="store-button store-button-primary mt-7">
                        Descobrir coleção
                    </a>
                </div>
            </div>

            <div class="store-panel absolute bottom-6 right-6 z-10 hidden rounded-full border px-5 py-2 text-[10px] font-semibold uppercase tracking-[.18em] md:block">
                Curadoria Malu · 2026
            </div>
        </div>
    </section>

    {{-- Categorias --}}
    @if ($categories->isNotEmpty())
        <section class="store-container home-container py-12 md:py-14">
            <div class="mb-8 text-center">
                <p class="store-kicker mb-2">
                    Encontre seu estilo
                </p>

                <h2 class="store-title text-3xl sm:text-4xl">
                    Escolha seu momento
                </h2>
            </div>

            <div class="flex flex-wrap justify-center gap-x-6 gap-y-6 sm:gap-x-8 md:gap-x-10">
                @foreach ($categories as $category)
                    @php
                        $categoryProduct = $category->products->first();
                        $categoryImage = $categoryProduct?->images->first();
                    @endphp

                    <a
                        href="{{ route('home', ['category' => $category->slug]) }}"
                        data-home-filter-link
                        class="group w-24 text-center sm:w-32"
                    >
                        <div class="store-soft-panel mx-auto aspect-square w-full max-w-24 overflow-hidden rounded-[2rem] border-4 {{ request('category') === $category->slug ? 'border-[var(--store-accent)] shadow-lg' : 'border-[var(--store-surface)]' }} shadow-sm transition duration-300 group-hover:-translate-y-1 group-hover:rotate-2 group-hover:border-[var(--store-accent)]/30 group-hover:shadow-lg sm:max-w-32">
                            @if ($categoryImage)
                                <img
                                    src="{{ $categoryImage->url }}"
                                    alt="{{ $category->name }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                                >
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <span class="store-title text-2xl text-stone-400">
                                        {{ mb_substr($category->name, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <p class="mt-3 text-xs font-bold uppercase tracking-[.12em] text-stone-700 transition group-hover:text-[var(--store-accent)]">
                            {{ $category->name }}
                        </p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Produtos --}}
    <section id="produtos" data-home-catalog-shell class="store-container home-container py-14 transition-opacity" aria-live="polite">
        <form action="{{ route('home') }}" method="GET" data-home-filter-form class="store-card mb-8 grid gap-3 p-4 sm:grid-cols-[minmax(0,1fr)_180px_160px]">
            <label class="relative block">
                <span class="sr-only">Buscar produtos na página inicial</span>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Busque uma roupa..." autocomplete="off" class="store-input w-full pl-10" data-home-live-search>
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-stone-400" aria-hidden="true">⌕</span>
            </label>

            <label>
                <span class="sr-only">Filtrar por categoria</span>
                <select name="category" class="store-input w-full" data-home-filter-control>
                    <option value="">Todas as categorias</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span class="sr-only">Ordenar produtos</span>
                <select name="sort" class="store-input w-full" data-home-filter-control>
                    <option value="recent" @selected(request('sort', 'recent') === 'recent')>Mais recentes</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Menor preço</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Maior preço</option>
                </select>
            </label>

            <div class="flex items-center justify-between gap-3 sm:col-span-3">
                <p class="text-[10px] text-stone-500">A vitrine muda automaticamente enquanto você pesquisa.</p>
                @if (request()->filled('search') || request()->filled('category') || request()->filled('sort'))
                    <a href="{{ route('home') }}#produtos" data-home-filter-link class="text-[11px] font-semibold text-[var(--store-accent-strong)] hover:underline">Limpar filtros</a>
                @endif
            </div>

            <noscript><button class="store-button store-button-primary sm:col-span-3">Filtrar produtos</button></noscript>
        </form>

        <div class="mb-8 flex items-center gap-2 sm:gap-4">
            <span class="h-px flex-1 bg-[var(--store-border)]"></span>

            <div class="text-center">
                <h2 class="store-title text-2xl sm:text-3xl">
                    {{ request('search') ? 'Resultados para “'.request('search').'”' : (request('category') ? 'Peças selecionadas' : 'Acabaram de chegar') }}
                </h2>
            </div>

            <span class="h-px flex-1 bg-[var(--store-border)]"></span>

            <a
                href="{{ route('catalog.index') }}"
                class="store-link shrink-0 text-[11px] font-semibold sm:text-xs"
            >
                Ver todas
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
            @forelse ($products as $product)
                <x-store.product-card :product="$product" />
            @empty
                <div class="col-span-full rounded-md border border-dashed border-[#e8d9d6] py-16 text-center text-stone-500">
                    <p>Nenhum produto encontrado com esses filtros.</p>
                    <a href="{{ route('home') }}#produtos" data-home-filter-link class="store-button store-button-outline mt-5 inline-flex">Limpar filtros</a>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Benefícios --}}
    <section class="store-panel border-y">
        <div class="store-container home-container grid gap-5 py-7 text-center sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-xl text-[var(--store-sage)]">▱</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-wider">Envio rápido</p>
                <p class="text-[10px] text-stone-500">para todo o Brasil</p>
            </div>

            <div>
                <p class="text-xl text-[var(--store-sage)]">♢</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-wider">Compra segura</p>
                <p class="text-[10px] text-stone-500">seus dados protegidos</p>
            </div>

            <div>
                <p class="text-xl text-[var(--store-sage)]">↺</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-wider">Troca fácil</p>
                <p class="text-[10px] text-stone-500">até 7 dias</p>
            </div>

            <div>
                <p class="text-xl text-[var(--store-sage)]">▤</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-wider">Parcele em até {{ \App\Models\StoreSetting::current()->max_installments }}x</p>
                <p class="text-[10px] text-stone-500">sem juros no cartão</p>
            </div>
        </div>
    </section>

    {{-- Galeria / Instagram --}}
    @php
        $galleryImages = $products
            ->flatMap(fn ($product) => $product->images)
            ->take(4);
    @endphp

    @if ($galleryImages->isNotEmpty())
        <section class="store-container home-container py-14">
            <div class="store-card grid overflow-hidden md:grid-cols-[.9fr_2.1fr]">
                <div class="bg-[var(--store-surface-accent)] p-8">
                    <p class="store-kicker">
                        #malustore
                    </p>

                    <h2 class="store-title mt-3 text-2xl">
                        Nos marque e apareça por aqui!
                    </h2>

                    <a href="#" class="store-button store-button-primary mt-6">
                        Ver no Instagram
                    </a>
                </div>

                <div class="store-panel grid grid-cols-4 gap-1.5 p-1.5">
                    @foreach ($galleryImages as $image)
                        <img
                            src="{{ $image->url }}"
                            alt="Malu Store"
                            class="aspect-[3/4] h-full w-full object-cover"
                        >
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    @vite('resources/js/home/catalog.js')
@endpush
