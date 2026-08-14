@props(['product'])

@php
    $stock = $product->variants->sum('stock');
    $image = $product->images->first();
@endphp

<article
    class="group relative overflow-hidden rounded-md border border-[#eee6e4] bg-white transition duration-300 hover:-translate-y-1 hover:shadow-[0_14px_35px_rgba(63,38,35,.09)]"
>
    <a href="{{ route('product.show', $product->id) }}" class="block">
        <div class="relative aspect-[3/4] overflow-hidden bg-[#f8f1ed]">
            @if ($image)
                <img
                    src="{{ asset('storage/products/' . $image->image) }}"
                    alt="{{ $product->name }}"
                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                >
            @else
                <div class="flex h-full items-center justify-center text-sm text-stone-400">
                    Sem imagem
                </div>
            @endif

            @if ($stock <= 0)
                <span
                    class="absolute left-3 top-3 bg-white px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-stone-600"
                >
                    Esgotado
                </span>
            @elseif ($product->created_at?->greaterThanOrEqualTo(now()->subDays(30)))
                <span
                    class="absolute left-3 top-3 rounded-sm bg-white px-2 py-1 text-[9px] font-bold uppercase tracking-wider text-[#bd5564]"
                >
                    Novo
                </span>
            @endif
        </div>

        <div class="p-4">
            <h3 class="min-h-10 text-sm font-medium text-stone-800">
                {{ $product->name }}
            </h3>

            <p class="mt-2 text-base font-bold text-stone-900">
                R$ {{ number_format($product->price, 2, ',', '.') }}
            </p>

            <p class="mt-1 text-xs text-stone-500">
                3x de R$ {{ number_format($product->price / 3, 2, ',', '.') }}
            </p>
        </div>
    </a>

    @auth
        @php
            $isFavorite = $product
                ->favorites()
                ->where('user_id', auth()->id())
                ->exists();
        @endphp

        <form
            action="{{ route('favorites.toggle', $product) }}"
            method="POST"
            class="absolute right-3 top-3"
        >
            @csrf

            <button
                type="submit"
                aria-label="Favoritar {{ $product->name }}"
                class="flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-sm text-[#d66f7c]"
            >
                {{ $isFavorite ? '♥' : '♡' }}
            </button>
        </form>
    @else
        <a
            href="{{ route('login') }}"
            aria-label="Entrar para favoritar"
            class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-full bg-white/90 text-sm text-[#d66f7c]"
        >
            ♡
        </a>
    @endauth
</article>
