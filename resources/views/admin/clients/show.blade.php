@extends('layouts.admin')

@section('title', 'Detalhes do Cliente')

@section('content')

<div class="min-h-screen flex justify-center px-4 py-10">


<div class="w-full max-w-3xl">

    <h1 class="text-4xl font-bold mb-8 text-center">
        Detalhes do Cliente
    </h1>

    <div class="bg-white shadow-md rounded-lg p-8 space-y-6">

        {{-- ID --}}
        <div>
            <p class="text-sm text-gray-500">ID</p>
            <p class="text-lg font-semibold">{{ $user->id }}</p>
        </div>

        {{-- Nome --}}
        <div>
            <p class="text-sm text-gray-500">Nome</p>
            <p class="text-lg font-semibold">{{ $user->name }}</p>
        </div>

        {{-- Email --}}
        <div>
            <p class="text-sm text-gray-500">Email</p>
            <p class="text-lg font-semibold">{{ $user->email }}</p>
        </div>

        {{-- Senha --}}
        <div>
            <p class="text-sm text-gray-500">Senha</p>
            <p class="text-lg font-semibold text-gray-400">
                ••••••••
            </p>
        </div>

        {{-- Telefone --}}
        <div>
            <p class="text-sm text-gray-500">Telefone</p>
            <p class="text-lg font-semibold">
                {{ $user->phone ?? '—' }}
            </p>
        </div>


        {{-- ENDEREÇOS --}}
        @if($user->addresses->count())

        <div class="pt-6 border-t">

            <p class="text-lg font-semibold mb-3">
                Endereços do Cliente
            </p>

            <div class="space-y-3">

                @foreach($user->addresses as $address)

                <div class="border rounded-lg p-4 bg-gray-50">

                    <p class="font-semibold">
                        {{ $address->label ?? 'Endereço' }}
                    </p>

                    <p class="text-sm text-gray-600">
                        {{ $address->street }}, {{ $address->number }}
                    </p>

                    @if($address->complement)
                    <p class="text-sm text-gray-600">
                        {{ $address->complement }}
                    </p>
                    @endif

                    <p class="text-sm text-gray-600">
                        {{ $address->neighborhood }}
                    </p>

                    <p class="text-sm text-gray-600">
                        {{ $address->city }} - {{ $address->state }}
                    </p>

                    <p class="text-sm text-gray-600">
                        CEP: {{ $address->cep }}
                    </p>

                </div>

                @endforeach

            </div>

        </div>

        @endif


        {{-- PEDIDOS --}}
        @if($user->orders->count())

        <div class="pt-6 border-t">

            <p class="text-lg font-semibold mb-4">
                Pedidos do Cliente
            </p>

            <div class="space-y-4">

                @foreach($user->orders as $order)

                <div class="border rounded-lg p-4">

                    <div class="flex justify-between mb-3">

                        <div>
                            <p class="font-semibold">
                                Pedido #{{ $order->id }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>

                        <div class="text-right">

                            <p class="font-semibold">
                                R$ {{ number_format($order->total, 2, ',', '.') }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ ucfirst($order->status) }}
                            </p>

                        </div>

                    </div>

                    {{-- ITENS DO PEDIDO --}}
                    <div class="space-y-2">

                        @foreach($order->items as $item)

                        <div class="flex justify-between text-sm">

                            <span>
                                {{ $item->name_snapshot }}
                                (x{{ $item->quantity }})
                            </span>

                            <span>
                                R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                            </span>

                        </div>

                        @endforeach

                    </div>

                </div>

                @endforeach

            </div>

        </div>

        @endif


        {{-- Datas --}}
        <div class="grid grid-cols-2 gap-4 pt-6 border-t">

            <div>
                <p class="text-sm text-gray-500">Criado em</p>
                <p class="font-medium">
                    {{ optional($user->created_at)->format('d/m/Y H:i') ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Última atualização</p>
                <p class="font-medium">
                    {{ optional($user->updated_at)->format('d/m/Y H:i') ?? '—' }}
                </p>
            </div>

        </div>


        {{-- BOTÃO --}}
        <div class="flex justify-center pt-6 border-t">

            <a href="{{ route('admin.clients.index') }}"
               class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition">
                Voltar
            </a>

        </div>

    </div>

</div>


</div>

@endsection
