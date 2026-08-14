@extends('layouts.public.app')

@section('title', 'Carrinho')

@section('content')
    <div class="store-container py-10 md:py-14">
        <h1 class="store-title mb-8 text-4xl md:text-5xl">
            Seu Carrinho
        </h1>

        @if (!$cart || $cart->items->isEmpty())
            <div class="rounded-md border border-[#eee6e4] bg-white p-10 text-center">
                <p class="text-lg text-stone-500">
                    Seu carrinho está vazio.
                </p>

                <a
                    href="{{ route('home') }}"
                    class="store-button store-button-primary mt-6"
                >
                    Continuar Comprando
                </a>
            </div>
        @else
            <div class="overflow-x-auto rounded-md border border-[#eee6e4] bg-white">
                <table class="w-full min-w-[760px] text-left">
                    <thead class="border-b border-[#ded5d2] bg-[#fff8f7] text-xs font-bold uppercase tracking-wide text-stone-700">
                        <tr>
                            <th class="p-5">Produto</th>
                            <th class="p-5">Preço</th>
                            <th class="p-5">Quantidade</th>
                            <th class="p-5">Subtotal</th>
                            <th class="p-5 text-center">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($cart->items as $item)
                            <tr class="border-t border-[#eee6e4] transition hover:bg-[#fffaf9]">
                                {{-- Produto --}}
                                <td class="flex items-center gap-4 p-5">
                                    <img
                                        src="{{ asset('storage/products/' . $item->image_snapshot) }}"
                                        alt="{{ $item->name_snapshot }}"
                                        class="h-24 w-20 rounded-sm object-cover"
                                    >

                                    <div>
                                        <p class="text-base font-semibold text-stone-800">
                                            {{ $item->name_snapshot }}
                                        </p>

                                        @if ($item->color_snapshot || $item->size_snapshot)
                                            <p class="mt-1 text-sm text-stone-500">
                                                @if ($item->color_snapshot)
                                                    Cor: {{ $item->color_snapshot }}
                                                @endif

                                                @if ($item->size_snapshot)
                                                    | Tamanho: {{ $item->size_snapshot }}
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                {{-- Preço --}}
                                <td class="p-5 font-medium text-stone-700">
                                    R$ {{ number_format($item->price, 2, ',', '.') }}
                                </td>

                                {{-- Quantidade --}}
                                <td class="p-5">
                                    <form
                                        action="{{ route('cart.update', $item->id) }}"
                                        method="POST"
                                        class="flex items-center gap-2"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <input
                                            type="number"
                                            name="quantity"
                                            value="{{ $item->quantity }}"
                                            min="1"
                                            class="w-20 rounded-md border border-stone-500 px-2 py-2 text-center"
                                        >

                                        <button
                                            type="submit"
                                            class="text-sm font-medium text-[#bd5564] hover:underline"
                                        >
                                            Atualizar
                                        </button>
                                    </form>
                                </td>

                                {{-- Subtotal --}}
                                <td class="p-5 text-lg font-bold text-stone-800">
                                    R$ {{ number_format($item->total, 2, ',', '.') }}
                                </td>

                                {{-- Remover --}}
                                <td class="p-5 text-center">
                                    <form
                                        action="{{ route('cart.remove', $item->id) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-sm font-medium text-red-500 hover:underline"
                                        >
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
                <div class="w-full max-w-md rounded-md border border-[#eee6e4] bg-white p-7 shadow-[0_10px_30px_rgba(63,38,35,.06)]">
                    <div class="mb-4 flex justify-between text-stone-600">
                        <span>Itens:</span>
                        <span>{{ $cart->total_items }}</span>
                    </div>

                    <div class="mb-6 flex justify-between text-2xl font-bold">
                        <span>Total:</span>

                        <span class="text-[#bd5564]">
                            R$ {{ number_format($cart->subtotal, 2, ',', '.') }}
                        </span>
                    </div>

                    <a
                        href="{{ route('checkout') }}"
                        class="store-button store-button-primary w-full py-4"
                    >
                        Finalizar Compra
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
