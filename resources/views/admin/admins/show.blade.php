@extends('layouts.admin')

@section('title', 'Detalhes do Administrador')

@section('content')

<div class="h-full flex items-center justify-center px-4">

    <div class="w-full max-w-3xl">

        <h1 class="text-4xl font-bold mb-8 text-center">
            Detalhes do Administrador
        </h1>

        <div class="bg-white shadow-md rounded-lg p-8 space-y-6">

            {{-- ID --}}
            <div>
                <p class="text-sm text-gray-500">ID</p>
                <p class="text-lg font-semibold">{{ $admin->id }}</p>
            </div>

            {{-- Nome --}}
            <div>
                <p class="text-sm text-gray-500">Nome</p>
                <p class="text-lg font-semibold">{{ $admin->name }}</p>
            </div>

            {{-- Email --}}
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="text-lg font-semibold">{{ $admin->email }}</p>
            </div>

            {{-- Cargo --}}
            <div>
                <p class="text-sm text-gray-500">Cargo</p>

                @php
                    $roles = [
                        'superadmin' => 'bg-purple-200 text-purple-800',
                        'admin'      => 'bg-blue-200 text-blue-800',
                        'suporte'    => 'bg-yellow-200 text-yellow-800',
                    ];
                    $class = $roles[$admin->role] ?? 'bg-gray-200 text-gray-800';
                @endphp

                <span class="px-3 py-1 text-sm rounded font-semibold {{ $class }}">
                    {{ ucfirst($admin->role) }}
                </span>
            </div>

            {{-- Status --}}
            <div>
                <p class="text-sm text-gray-500">Status</p>

                @if($admin->is_active)
                    <span class="px-3 py-1 text-sm rounded bg-green-200 text-green-800 font-semibold">
                        ● Ativo
                    </span>
                @else
                    <span class="px-3 py-1 text-sm rounded bg-red-200 text-red-800 font-semibold">
                        ● Inativo
                    </span>
                @endif
            </div>

            {{-- Datas --}}
            <div class="grid grid-cols-2 gap-4 pt-6 border-t">

                <div>
                    <p class="text-sm text-gray-500">Criado em</p>
                    <p class="font-medium">
                        {{ $admin->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Última atualização</p>
                    <p class="font-medium">
                        {{ $admin->updated_at->format('d/m/Y H:i') }}
                    </p>
                </div>

            </div>

            {{-- BOTÕES --}}
            <div class="flex justify-between items-center pt-6 border-t">

                <a href="{{ route('admin.admins.index') }}"
                   class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition">
                    Voltar
                </a>

                <a href="{{ route('admin.admins.edit', $admin) }}"
                   class="bg-yellow-500 text-white px-6 py-2 rounded-md hover:bg-yellow-600 transition">
                    Editar
                </a>

            </div>

        </div>

    </div>

</div>

@endsection
