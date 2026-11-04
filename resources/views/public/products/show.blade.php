@extends('layouts.public.app')

@section('title', $product->name)

@push('head')
    <meta name="description" content="{{ Str::limit($product->description, 155) }}">
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $product->name }} | Malu Store">
    <meta property="og:description" content="{{ Str::limit($product->description, 180) }}">
    <meta property="og:url" content="{{ route('product.show', $product) }}">
    @if ($product->images->isNotEmpty())
        <meta property="og:image" content="{{ $product->images->first()->url }}">
    @endif
    <meta property="product:price:amount" content="{{ $product->price }}">
    <meta property="product:price:currency" content="BRL">

    @php
        $structuredProduct = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->description,
            'sku' => 'MALU-'.$product->id,
            'brand' => ['@type' => 'Brand', 'name' => 'Malu Store'],
            'image' => $product->images->map->url->values()->all(),
            'category' => $product->category?->name,
            'offers' => [
                '@type' => 'Offer',
                'url' => route('product.show', $product),
                'priceCurrency' => 'BRL',
                'price' => $product->price,
                'priceValidUntil' => now()->addYear()->toDateString(),
                'availability' => $product->variants->sum('stock') > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/OutOfStock',
                'itemCondition' => 'https://schema.org/NewCondition',
            ],
        ];

        if ((int) ($verifiedReviewSummary?->count ?? 0) > 0) {
            $structuredProduct['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round((float) $verifiedReviewSummary->average, 1),
                'reviewCount' => (int) $verifiedReviewSummary->count,
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }
    @endphp
    <script type="application/ld+json" nonce="{{ Vite::cspNonce() }}">{!! json_encode($structuredProduct, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    @php
        $availableVariants = $product->variants->where('stock', '>', 0);
        $stock = $availableVariants->sum('stock');
        $colors = $availableVariants->pluck('color')->filter()->unique();
        $sizes = $availableVariants->pluck('size')->filter()->unique();
        $variantOptions = $availableVariants
            ->map(fn ($variant) => [
                'id' => $variant->id,
                'color' => $variant->color,
                'size' => $variant->size,
                'stock' => $variant->stock,
            ])
            ->values();
    @endphp

    <div class="store-container py-7 md:py-10">
        {{-- Breadcrumb --}}
        <nav class="mb-6 text-xs text-stone-500" aria-label="Navegação estrutural">
            <a href="{{ route('home') }}" class="hover:underline">Home</a> <span class="mx-1">›</span>
            <a href="{{ route('catalog.index', ['category' => $product->category?->slug]) }}" class="hover:underline">{{ $product->category?->name }}</a>
            <span class="mx-1">›</span>
            <span aria-current="page">{{ $product->name }}</span>
        </nav>

        <div class="grid gap-9 lg:grid-cols-[1.05fr_.95fr] lg:gap-12">
            {{-- Galeria --}}
            <div data-product-gallery class="flex gap-3">
                <div class="hidden w-16 shrink-0 space-y-2 sm:block">
                    @foreach ($product->images as $image)
                        <button
                            type="button"
                            class="block h-20 w-16 overflow-hidden rounded-sm"
                            data-product-gallery-thumbnail="{{ $image->url }}"
                            data-product-gallery-alt="{{ $product->name }}"
                        >
                            <img
                                src="{{ $image->url }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover object-center"
                            >
                        </button>
                    @endforeach
                </div>

                <div class="min-w-0 flex-1 overflow-hidden">
                    @if ($product->images->isNotEmpty())
                        <img
                            data-product-gallery-main
                            src="{{ $product->images->first()->url }}"
                            alt="{{ $product->name }}"
                            class="aspect-[3/4] w-full object-cover object-center"
                        >
                    @endif
                </div>
            </div>

            {{-- Informações do produto --}}
            <div>
                <h1 class="store-title text-4xl leading-none text-stone-900">
                    {{ $product->name }}
                </h1>

                <p class="mt-3 text-sm tracking-wide text-amber-500">
                    @if ($product->reviews_count > 0)
                        {{ str_repeat('★', (int) round($product->reviews_avg_rating)) }}{{ str_repeat('☆', 5 - (int) round($product->reviews_avg_rating)) }}
                    @else
                        ☆☆☆☆☆
                    @endif
                    <span class="ml-1 text-xs text-stone-500">
                        @if ($product->reviews_count > 0)
                            {{ number_format($product->reviews_avg_rating, 1, ',', '.') }} · {{ $product->reviews_count }} {{ $product->reviews_count === 1 ? 'avaliação' : 'avaliações' }}
                        @else
                            Ainda sem avaliações
                        @endif
                    </span>
                </p>

                <p class="mt-5 text-3xl font-bold text-stone-900">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </p>

                <p class="mt-1 text-xs text-stone-600">
                    3x de R$ {{ number_format($product->price / 3, 2, ',', '.') }} sem juros
                </p>

                <p class="mt-6 max-w-md text-sm leading-6 text-stone-600">
                    {{ $product->description }}
                </p>

                <form
                    action="{{ route('cart.add') }}"
                    method="POST"
                    class="mt-6"
                    data-product-variant-form
                    data-product-variants='@json($variantOptions)'
                    data-product-initial-color="{{ $colors->first() }}"
                    data-product-initial-size="{{ $sizes->first() }}"
                >
                    @csrf

                    <p class="mb-2 text-xs font-bold text-stone-700">
                        Cor:
                        <span class="font-normal" data-selected-color-name>
                            {{ $colors->first() ?? 'Única' }}
                        </span>
                    </p>

                    <div class="mb-5 flex flex-wrap gap-2">
                        @foreach ($colors as $color)
                            <button
                                type="button"
                                data-product-color="{{ $color }}"
                                class="product-color-option inline-flex min-h-10 items-center gap-2 rounded-full border border-[var(--store-border)] px-3 py-2 text-xs font-semibold"
                                aria-label="Selecionar cor {{ $color }}"
                                aria-pressed="false"
                            >
                                <span
                                    class="h-5 w-5 rounded-full border border-black/15 shadow-inner"
                                    style="background-color: {{ \App\Support\ProductColor::css($color) }}"
                                    aria-hidden="true"
                                ></span>
                                <span>{{ $color }}</span>
                            </button>
                        @endforeach
                    </div>

                    @if ($sizes->isNotEmpty())
                        <p class="mb-2 text-xs font-bold text-stone-700">
                            Tamanho:
                        </p>

                        <div class="mb-3 flex flex-wrap gap-2">
                            @foreach ($sizes as $size)
                                <button
                                    type="button"
                                    data-product-size="{{ $size }}"
                                    class="product-size-option flex h-9 items-center justify-center rounded-md px-3 text-xs"
                                    aria-label="Selecionar tamanho {{ $size }}"
                                    aria-pressed="false"
                                >
                                    {{ $size }}
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <input
                        type="hidden"
                        name="variant_id"
                        value="{{ $availableVariants->first()?->id }}"
                    >

                    <button type="button" data-size-guide-open class="text-xs font-semibold underline underline-offset-4">
                        Guia de medidas
                    </button>

                    <p class="mt-3 text-xs text-stone-500" data-variant-stock aria-live="polite"></p>
                    <p class="mt-2 text-[11px] leading-5 text-stone-500">{{ $modelInformation }}</p>

                    {{-- Estimativa antes da compra; a opção final é escolhida no checkout. --}}
                    <section
                        data-product-shipping
                        data-product-id="{{ $product->id }}"
                        data-endpoint="{{ route('frete.calcular') }}"
                        class="store-panel mt-5 rounded-md border p-4"
                        aria-labelledby="product-shipping-title"
                    >
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 text-base text-[#bd5564]" aria-hidden="true">⌖</span>
                            <div>
                                <h2 id="product-shipping-title" class="text-sm font-bold text-stone-800">
                                    Calcule o frete
                                </h2>
                                <p class="mt-1 text-xs text-stone-500">
                                    Consulte prazo e valor antes de adicionar à sacola.
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 flex gap-2">
                            <input
                                data-shipping-cep
                                type="text"
                                inputmode="numeric"
                                autocomplete="postal-code"
                                maxlength="9"
                                class="store-input min-w-0 flex-1 py-2 text-xs"
                                placeholder="Digite seu CEP"
                                aria-label="CEP para cálculo de frete"
                            >

                            <button
                                data-shipping-submit
                                type="button"
                                class="store-button store-button-outline shrink-0 px-4 py-2 text-[10px]"
                            >
                                Calcular
                            </button>
                        </div>

                        <p data-shipping-feedback class="mt-3 text-[11px] text-stone-500" aria-live="polite">
                            O frete definitivo é escolhido no checkout.
                        </p>

                        <ul data-shipping-results class="store-soft-panel mt-3 hidden rounded-md p-3"></ul>
                    </section>

                    <input type="hidden" name="quantity" value="1">

                    @if ($stock > 0)
                        <button class="store-button store-button-primary mt-5 w-full">
                            ♧ Adicionar à sacola
                        </button>
                    @else
                        <button
                            disabled
                            class="mt-5 w-full rounded bg-stone-300 py-3 text-xs text-white"
                        >
                            Produto esgotado
                        </button>
                    @endif

                    <div class="mt-4 grid gap-2 text-[11px] text-stone-600 sm:grid-cols-2">
                        <p class="rounded-md border border-[var(--store-border)] px-3 py-2">✓ Postagem em até 2 dias úteis</p>
                        <p class="rounded-md border border-[var(--store-border)] px-3 py-2">✓ Troca fácil em até 7 dias</p>
                        <p class="rounded-md border border-[var(--store-border)] px-3 py-2">✓ Pagamento processado com segurança</p>
                        <p class="rounded-md border border-[var(--store-border)] px-3 py-2">✓ Atendimento humano via WhatsApp</p>
                    </div>

                    @auth
                        @php
                            $isFavorite = $product
                                ->favorites()
                                ->where('user_id', auth()->id())
                                ->exists();
                        @endphp

                        <button
                            form="favorite-form"
                            type="submit"
                            class="store-button store-button-outline mt-2 w-full"
                        >
                            {{ $isFavorite ? '♥ Remover dos favoritos' : '♡ Adicionar aos favoritos' }}
                        </button>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="store-button store-button-outline mt-2 w-full"
                        >
                            ♡ Entrar para favoritar
                        </a>
                    @endauth
                </form>

                @auth
                    <form
                        id="favorite-form"
                        action="{{ route('favorites.toggle', $product) }}"
                        method="POST"
                        class="hidden"
                    >
                        @csrf
                    </form>
                @endauth
            </div>
        </div>

        <dialog data-size-guide-dialog class="m-auto w-[min(92vw,680px)] rounded-xl border border-[var(--store-border)] bg-[var(--store-surface)] p-0 text-[var(--store-text)] shadow-2xl backdrop:bg-black/60">
            <div class="flex items-start justify-between border-b border-[var(--store-border)] p-5">
                <div>
                    <h2 class="store-title text-3xl">Guia de medidas</h2>
                    <p class="mt-1 text-xs text-stone-500">Medidas corporais aproximadas em centímetros.</p>
                </div>
                <button type="button" data-size-guide-close class="rounded-full border border-[var(--store-border)] px-3 py-1.5" aria-label="Fechar guia de medidas">×</button>
            </div>
            <div class="p-5">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[480px] text-left text-xs">
                        <thead class="bg-[var(--store-surface-soft)] text-[var(--store-text)]">
                            <tr><th class="p-3">Tamanho</th><th class="p-3">Busto</th><th class="p-3">Cintura</th><th class="p-3">Quadril</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($sizeGuideRows as $row)
                                <tr class="border-b border-[var(--store-border)]"><th class="p-3">{{ $row['size'] }}</th><td class="p-3">{{ $row['bust'] }}</td><td class="p-3">{{ $row['waist'] }}</td><td class="p-3">{{ $row['hip'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-5 space-y-2 text-xs leading-5 text-stone-600">
                    <p><strong>Como medir:</strong> mantenha a fita paralela ao chão, sem apertar o corpo.</p>
                    <p><strong>Dúvida entre dois tamanhos?</strong> Prefira o maior para um caimento mais confortável.</p>
                    <p class="text-[11px] text-stone-500">Tabela provisória. As medidas da peça podem variar até 2 cm conforme o lote e a modelagem.</p>
                </div>
            </div>
        </dialog>

        <x-public.product.benefits />
        <x-public.product.details
            :product="$product"
            :reviews="$reviews"
            :eligible-review-items="$eligibleReviewItems"
        />

        {{-- Produtos relacionados --}}
        @if ($relatedProducts->isNotEmpty())
            <section class="mt-8">
                <h2 class="mb-5 text-center text-xs font-bold uppercase tracking-wide">
                    Você também pode gostar
                </h2>

                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <x-store.product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    @vite('resources/js/products/show.js')
@endpush
