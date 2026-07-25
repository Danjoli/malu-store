@extends('layouts.admin.app')

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
        @include('admins.cliente.show.addresses', [
            'addresses' => $user->addresses
        ])

        {{-- PEDIDOS --}}
        @include('admins.cliente.show.orders', [
            'orders' => $user->orders
        ])

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
