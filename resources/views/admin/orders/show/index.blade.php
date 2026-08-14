@extends('layouts.admin.app')

@section('title', 'Pedido')

@section('content')
    <div class="mx-auto max-w-5xl">
        <h1 class="mb-6 text-3xl font-bold">
            Pedido #{{ $order->id }}
        </h1>

        {{-- Informações do pedido --}}
        <div class="space-y-6 rounded-lg bg-white p-6 shadow">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">
                        Cliente
                    </p>

                    <p class="font-semibold">
                        {{ $order->user->name }}
                    </p>

                    <p class="text-sm text-gray-600">
                        {{ $order->user->email }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    <p class="font-semibold capitalize">
                        {{ $order->status }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Subtotal
                    </p>

                    <p class="font-semibold">
                        R$ {{ number_format($order->subtotal, 2, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Frete
                    </p>

                    <p class="font-semibold">
                        R$ {{ number_format($order->shipping, 2, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Total
                    </p>

                    <p class="text-lg font-semibold">
                        R$ {{ number_format($order->total, 2, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Pagamento
                    </p>

                    <p class="font-semibold">
                        {{ $order->payment_method ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Itens do pedido --}}
        @include('admin.orders.show.items', [
            'items' => $order->items,
        ])

        {{-- Endereço de entrega --}}
        @include('admin.orders.show.address', [
            'order' => $order,
        ])

        {{-- Voltar --}}
        <div class="mt-6">
            <a
                href="{{ route('admin.orders.index') }}"
                class="rounded bg-gray-600 px-6 py-2 text-white hover:bg-gray-700"
            >
                Voltar
            </a>
        </div>
    </div>
@endsection
