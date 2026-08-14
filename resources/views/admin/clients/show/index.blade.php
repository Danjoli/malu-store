@extends('layouts.admin.app')

@section('title', 'Detalhes do Cliente')

@section('content')
    <div class="flex min-h-screen justify-center px-4 py-10">
        <div class="w-full max-w-3xl">
            <h1 class="mb-8 text-center text-4xl font-bold">
                Detalhes do Cliente
            </h1>

            <div class="space-y-6 rounded-lg bg-white p-8 shadow-md">
                {{-- ID --}}
                <div>
                    <p class="text-sm text-gray-500">
                        ID
                    </p>

                    <p class="text-lg font-semibold">
                        {{ $user->id }}
                    </p>
                </div>

                {{-- Nome --}}
                <div>
                    <p class="text-sm text-gray-500">
                        Nome
                    </p>

                    <p class="text-lg font-semibold">
                        {{ $user->name }}
                    </p>
                </div>

                {{-- Email --}}
                <div>
                    <p class="text-sm text-gray-500">
                        Email
                    </p>

                    <p class="text-lg font-semibold">
                        {{ $user->email }}
                    </p>
                </div>

                {{-- Senha --}}
                <div>
                    <p class="text-sm text-gray-500">
                        Senha
                    </p>

                    <p class="text-lg font-semibold text-gray-400">
                        ••••••••
                    </p>
                </div>

                {{-- Telefone --}}
                <div>
                    <p class="text-sm text-gray-500">
                        Telefone
                    </p>

                    <p class="text-lg font-semibold">
                        {{ $user->phone ?? '—' }}
                    </p>
                </div>

                {{-- Endereços --}}
                @include('admins.cliente.show.addresses', [
                    'addresses' => $user->addresses,
                ])

                {{-- Pedidos --}}
                @include('admins.cliente.show.orders', [
                    'orders' => $user->orders,
                ])

                {{-- Datas --}}
                <div class="grid grid-cols-2 gap-4 border-t pt-6">
                    <div>
                        <p class="text-sm text-gray-500">
                            Criado em
                        </p>

                        <p class="font-medium">
                            {{ optional($user->created_at)->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Última atualização
                        </p>

                        <p class="font-medium">
                            {{ optional($user->updated_at)->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Botão --}}
                <div class="flex justify-center border-t pt-6">
                    <a
                        href="{{ route('admin.clients.index') }}"
                        class="rounded-md bg-gray-600 px-6 py-2 text-white transition hover:bg-gray-700"
                    >
                        Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
