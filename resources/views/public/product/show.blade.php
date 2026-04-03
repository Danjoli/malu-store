@extends('layouts.app')

@section('title', $product->name)

@section('content')

<div class="container mx-auto px-4 py-10">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        {{-- GALERIA --}}
        <div>
            <div class="bg-white rounded-xl shadow p-4">

                @if($product->images->count())
                    <img id="mainImage"
                         src="{{ asset('products/' . $product->images->first()->image) }}"
                         class="w-full h-[450px] object-cover rounded-lg">
                @endif

                <div class="flex gap-3 mt-4">
                    @foreach($product->images as $img)
                        <img src="{{ asset('products/' . $img->image) }}"
                             class="w-20 h-20 object-cover rounded cursor-pointer border"
                             onclick="document.getElementById('mainImage').src=this.src">
                    @endforeach
                </div>

            </div>
        </div>

        {{-- INFORMAÇÕES --}}
        <div class="bg-white rounded-xl shadow p-6 flex flex-col">

            <h1 class="text-3xl font-bold mb-2">{{ $product->name }}</h1>

            <p class="text-gray-500 mb-4">{{ $product->description }}</p>

            @php
                $stock = $product->variants->sum('stock');
            @endphp

            @if($stock > 0)
                <span class="text-green-600 font-semibold mb-3">✔ Em estoque</span>
            @else
                <span class="text-red-500 font-semibold mb-3">✖ Esgotado</span>
            @endif

            <div class="text-4xl font-bold text-blue-600 mb-6">
                R$ {{ number_format($product->price, 2, ',', '.') }}
            </div>

            {{-- FORM --}}
            <form action="{{ route('cart.add') }}" method="POST" class="mt-auto">
                @csrf

                @if($product->variants->where('stock','>',0)->count())
                    <div class="mb-6">
                        <label class="block font-semibold mb-2">
                            Escolha a variação:
                        </label>

                        <select name="variant_id"
                                class="w-full border rounded p-2"
                                required>

                            @foreach($product->variants->where('stock', '>', 0) as $variant)
                                <option value="{{ $variant->id }}">
                                    {{ $variant->size ?? '' }}
                                    {{ $variant->color ?? '' }}
                                    ({{ $variant->stock }} disponíveis)
                                </option>
                            @endforeach

                        </select>
                    </div>
                @endif

                <input type="hidden" name="quantity" value="1">

                @if($stock > 0)

                    <button type="submit"
                        class="bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition w-full">
                        🛒 Adicionar ao carrinho
                    </button>

                @else

                    <button type="button"
                        disabled
                        class="bg-gray-400 text-white py-3 rounded-lg cursor-not-allowed w-full">
                        Produto esgotado
                    </button>

                @endif

            </form>

        </div>

    </div>
</div>

@endsection
