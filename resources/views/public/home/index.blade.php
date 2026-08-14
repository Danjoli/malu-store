@extends('layouts.public.app')

@section('title', 'Loja')

@section('content')
    {{-- Banner principal --}}
    <section class="border-b border-[#eee6e4] bg-[#f8eee9]">
        <div class="relative min-h-[440px] overflow-hidden sm:min-h-[410px] md:min-h-[450px]">
            <img
                src="{{ asset('storage/products/hero-malu-store.png') }}"
                alt="Nova coleção Malu Store"
                class="absolute inset-0 h-full w-full object-cover object-[64%_center] sm:object-[65%_center]"
            >

            <div class="absolute inset-0 bg-gradient-to-r from-[#f8eee9] via-[#f8eee9]/90 to-[#f8eee9]/10 sm:via-[#f8eee9]/78 sm:to-transparent"></div>

            <div class="store-container home-container relative z-10 flex min-h-[440px] items-center py-12 sm:min-h-[410px] md:min-h-[450px]">
                <div class="max-w-[17rem] sm:max-w-md">
                    <p class="store-kicker mb-5 text-[#bd5564]">
                        Nova coleção
                    </p>

                    <h1 class="store-title text-[2.25rem] leading-[1.02] text-stone-900 sm:text-4xl md:text-5xl">
                        Elegância para todos os momentos
                    </h1>

                    <a href="#produtos" class="store-button store-button-primary mt-7">
                        Comprar agora
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Categorias --}}
    @php
        $categories = $products->pluck('category')->filter()->unique('id');
    @endphp

    @if ($categories->isNotEmpty())
        <section class="store-container home-container py-12 md:py-14">
            <div class="mb-8 text-center">
                <p class="store-kicker mb-2 text-[#bd5564]">
                    Encontre seu estilo
                </p>

                <h2 class="store-title text-3xl">
                    Categorias
                </h2>
            </div>

            <div class="grid grid-cols-3 gap-x-3 gap-y-6 sm:grid-cols-6 sm:gap-4 md:gap-5">
                @foreach ($categories as $category)
                    @php
                        $categoryProduct = $products->firstWhere('category_id', $category->id);
                        $categoryImage = $categoryProduct?->images->first();
                    @endphp

                    <a
                        href="{{ route('home', ['category' => $category->slug]) }}"
                        class="group text-center"
                    >
                        <div class="mx-auto aspect-square w-full max-w-24 overflow-hidden rounded-full border-4 border-[#f8eee9] bg-[#f1e4de] shadow-sm transition duration-300 group-hover:-translate-y-1 group-hover:border-[#f4cfca] group-hover:shadow-lg sm:max-w-32">
                            @if ($categoryImage)
                                <img
                                    src="{{ asset('storage/products/' . $categoryImage->image) }}"
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

                        <p class="mt-3 text-xs font-bold uppercase tracking-wider text-stone-700 transition group-hover:text-[#bd5564]">
                            {{ $category->name }}
                        </p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Produtos --}}
    <section id="produtos" class="store-container home-container py-14">
        <div class="mb-8 flex items-center gap-2 sm:gap-4">
            <span class="h-px flex-1 bg-[#eadfdd]"></span>

            <div class="text-center">
                <h2 class="store-title text-2xl sm:text-3xl">
                    Novidades
                </h2>
            </div>

            <span class="h-px flex-1 bg-[#eadfdd]"></span>

            <a
                href="{{ route('home') }}"
                class="shrink-0 text-[11px] font-semibold text-stone-700 transition hover:text-[#bd5564] sm:text-xs"
            >
                Ver todas
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
            @forelse ($products as $product)
                <x-store.product-card :product="$product" />
            @empty
                <div class="col-span-full rounded-md border border-dashed border-[#e8d9d6] py-16 text-center text-stone-500">
                    Nenhum produto disponível no momento.
                </div>
            @endforelse
        </div>
    </section>

    {{-- Benefícios --}}
    <section class="border-y border-[#eee6e4] bg-[#fff8f7]">
        <div class="store-container home-container grid gap-5 py-7 text-center sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-lg">▱</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-wider">Envio rápido</p>
                <p class="text-[10px] text-stone-500">para todo o Brasil</p>
            </div>

            <div>
                <p class="text-lg">♢</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-wider">Compra segura</p>
                <p class="text-[10px] text-stone-500">seus dados protegidos</p>
            </div>

            <div>
                <p class="text-lg">↺</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-wider">Troca fácil</p>
                <p class="text-[10px] text-stone-500">até 7 dias</p>
            </div>

            <div>
                <p class="text-lg">▤</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-wider">Parcele em até 6x</p>
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
            <div class="grid overflow-hidden rounded-md border border-[#eee6e4] md:grid-cols-[.9fr_2.1fr]">
                <div class="bg-[#fff1ef] p-8">
                    <p class="store-kicker text-[#bd5564]">
                        #malustore
                    </p>

                    <h2 class="store-title mt-3 text-2xl">
                        Nos marque e apareça por aqui!
                    </h2>

                    <a href="#" class="store-button store-button-primary mt-6">
                        Ver no Instagram
                    </a>
                </div>

                <div class="grid grid-cols-4 gap-1.5 bg-white p-1.5">
                    @foreach ($galleryImages as $image)
                        <img
                            src="{{ asset('storage/products/' . $image->image) }}"
                            alt="Malu Store"
                            class="aspect-[3/4] h-full w-full object-cover"
                        >
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
