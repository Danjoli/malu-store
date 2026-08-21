@extends('layouts.admin.app')

@section('title', 'Detalhes do Administrador')

@section('content')
    <div class="flex h-full items-center justify-center px-4">
        <div class="w-full max-w-3xl">
            <h1 class="mb-8 text-center text-4xl font-bold">
                Detalhes do Administrador
            </h1>

            <div class="space-y-6 rounded-lg bg-white p-8 shadow-md">
                {{-- ID --}}
                <div>
                    <p class="text-sm text-gray-500">
                        ID
                    </p>

                    <p class="text-lg font-semibold">
                        {{ $admin->id }}
                    </p>
                </div>

                {{-- Nome --}}
                <div>
                    <p class="text-sm text-gray-500">
                        Nome
                    </p>

                    <p class="text-lg font-semibold">
                        {{ $admin->name }}
                    </p>
                </div>

                {{-- Email --}}
                <div>
                    <p class="text-sm text-gray-500">
                        Email
                    </p>

                    <p class="text-lg font-semibold">
                        {{ $admin->email }}
                    </p>
                </div>

                {{-- Cargo --}}
                <div>
                    <p class="text-sm text-gray-500">
                        Cargo
                    </p>

                    <span class="rounded px-3 py-1 text-sm font-semibold {{ $admin->role?->badgeClass() ?? 'bg-gray-200 text-gray-800' }}">
                        {{ $admin->role?->label() ?? 'Sem cargo' }}
                    </span>
                </div>

                {{-- Status --}}
                <div>
                    <p class="text-sm text-gray-500">
                        Status
                    </p>

                    @if ($admin->is_active)
                        <span class="rounded bg-green-200 px-3 py-1 text-sm font-semibold text-green-800">
                            ● Ativo
                        </span>
                    @else
                        <span class="rounded bg-red-200 px-3 py-1 text-sm font-semibold text-red-800">
                            ● Inativo
                        </span>
                    @endif
                </div>

                {{-- Datas --}}
                <div class="grid grid-cols-2 gap-4 border-t pt-6">
                    <div>
                        <p class="text-sm text-gray-500">
                            Criado em
                        </p>

                        <p class="font-medium">
                            {{ $admin->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Última atualização
                        </p>

                        <p class="font-medium">
                            {{ $admin->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>

                {{-- Botões --}}
                <div class="flex items-center justify-between border-t pt-6">
                    <a
                        href="{{ route('admin.admins.index') }}"
                        class="rounded-md bg-gray-600 px-6 py-2 text-white transition hover:bg-gray-700"
                    >
                        Voltar
                    </a>

                    <a
                        href="{{ route('admin.admins.edit', $admin) }}"
                        class="rounded-md bg-yellow-500 px-6 py-2 text-white transition hover:bg-yellow-600"
                    >
                        Editar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
