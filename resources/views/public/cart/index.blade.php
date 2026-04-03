@extends('layouts.app')

@section('title', 'Carrinho')

@section('content')
<div class="container mx-auto px-6 py-10">

    <h1 class="text-3xl font-bold mb-8">Seu Carrinho</h1>

    @if(!$cart || $cart->items->isEmpty())

        <div class="bg-white p-10 rounded-2xl shadow text-center">
            <p class="text-gray-500 text-lg">Seu carrinho está vazio.</p>

            <a href="{{ route('shop.index') }}"
               class="inline-block mt-6 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Continuar Comprando
            </a>
        </div>

    @else

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-100 text-sm text-gray-600 uppercase">
                <tr>
                    <th class="p-5">Produto</th>
                    <th class="p-5">Preço</th>
                    <th class="p-5">Quantidade</th>
                    <th class="p-5">Subtotal</th>
                    <th class="p-5 text-center">Ações</th>
                </tr>
            </thead>

            <tbody>

                @foreach($cart->items as $item)
                <tr class="border-t hover:bg-gray-50 transition">

                    {{-- Produto --}}
                    <td class="p-5 flex items-center gap-4">
                        <img src="{{ asset('products/' . $item->image_snapshot) }}"
                             class="w-20 h-20 object-cover rounded-xl shadow-sm">

                        <div>
                            <p class="font-semibold text-gray-800">
                                {{ $item->name_snapshot }}
                            </p>

                            @if($item->color_snapshot || $item->size_snapshot)
                                <p class="text-sm text-gray-500 mt-1">
                                    @if($item->color_snapshot)
                                        Cor: {{ $item->color_snapshot }}
                                    @endif

                                    @if($item->size_snapshot)
                                        | Tamanho: {{ $item->size_snapshot }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </td>

                    {{-- Preço --}}
                    <td class="p-5 font-medium text-gray-700">
                        R$ {{ number_format($item->price, 2, ',', '.') }}
                    </td>

                    {{-- Quantidade --}}
                    <td class="p-5">
                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')

                            <input type="number"
                                   name="quantity"
                                   value="{{ $item->quantity }}"
                                   min="1"
                                   class="w-20 border rounded-lg px-2 py-1 text-center">

                            <button type="submit"
                                    class="text-blue-600 hover:underline text-sm">
                                Atualizar
                            </button>
                        </form>
                    </td>

                    {{-- Subtotal --}}
                    <td class="p-5 font-semibold text-gray-800">
                        R$ {{ number_format($item->total, 2, ',', '.') }}
                    </td>

                    {{-- Remover --}}
                    <td class="p-5 text-center">
                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="text-red-600 hover:underline text-sm">
                                Remover
                            </button>
                        </form>
                    </td>

                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    {{-- Resumo --}}
    <div class="mt-10 flex justify-end">
        <div class="bg-white p-8 rounded-2xl shadow w-96">

            <div class="flex justify-between mb-3 text-gray-600">
                <span>Itens:</span>
                <span>{{ $cart->total_items }}</span>
            </div>

            <div class="flex justify-between mb-5 text-xl font-bold">
                <span>Total:</span>
                <span class="text-blue-600">
                    R$ {{ number_format($cart->subtotal, 2, ',', '.') }}
                </span>
            </div>

            <a href="{{ route('checkout') }}"
               class="block w-full bg-blue-600 text-white text-center py-3 rounded-xl hover:bg-blue-700 transition font-semibold">
                Finalizar Compra
            </a>

        </div>
    </div>

    @endif

</div>
@endsection
