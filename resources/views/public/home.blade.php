@extends('layouts.app')

@section('title', 'Loja')

@section('content')
    <div class="container mx-auto px-4 py-10">

        {{-- FILTROS --}}
        @include('public.components.filters')

        <h1 class="text-4xl font-bold mb-8 text-center">Nossos Produtos</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">

            @forelse ($products as $product)
            <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden flex flex-col">

                {{-- IMAGEM --}}
                <div class="h-56 bg-gray-100 flex items-center justify-center">
                    @if($product->images->count())
                        <img src="{{ asset('products/' . $product->images->first()->image) }}"
                         class="h-full w-full object-cover">
                    @else
                        <span class="text-gray-400 text-sm">Sem imagem</span>
                    @endif
                </div>

                {{-- CONTEÚDO --}}
                <div class="p-4 flex flex-col flex-1">
                    <h2 class="text-lg font-semibold mb-2">
                        {{ $product->name }}
                    </h2>

                    {{-- ESTOQUE --}}
                    @php
                        $stock = $product->variants->sum('stock');
                    @endphp

                    @if($stock > 0)
                        <span class="text-green-600 text-sm font-medium mb-2">✔ Em estoque</span>
                    @else
                        <span class="text-red-500 text-sm font-medium mb-2">✖ Esgotado</span>
                    @endif

                    {{-- PREÇO --}}
                    <p class="text-2xl font-bold text-blue-600 mt-auto">R$ {{ number_format($product->price, 2, ',', '.') }}</p>

                    <a href="{{ route('product.show' , $product->id) }}" class="mt-4 bg-blue-600 text-white text-center py-2 rounded hover:bg-blue-700 transition">
                        Ver Produto
                    </a>
                </div>
            </div>

            @empty
                <div class="col-span-full text-center text-gray-500 text-lg">
                    Nenhum produto disponível no momento.
                </div>
            @endforelse

        </div>
    </div>
@endsection
