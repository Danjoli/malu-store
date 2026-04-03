@extends('layouts.admin')

@section('title', 'Administradores')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Administradores</h1>

    <a href="{{ route('admin.admins.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow">
        + Novo Admin
    </a>
</div>

<div class="bg-white shadow rounded overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="p-3">Nome</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Cargo</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse($admins as $admin)
                <tr class="border-t hover:bg-gray-50 transition">
                    <td class="p-3 font-medium">{{ $admin->name }}</td>

                    <td class="p-3 text-gray-600">{{ $admin->email }}</td>

                    {{-- CARGO --}}
                    <td class="p-3">
                        @php
                            $roles = [
                                'superadmin' => 'bg-red-200 text-red-800',
                                'admin'      => 'bg-blue-200 text-blue-800',
                                'suporte'    => 'bg-green-200 text-green-800',
                            ];
                            $class = $roles[$admin->role] ?? 'bg-gray-200 text-gray-800';
                        @endphp

                        <span class="px-2 py-1 text-xs rounded font-semibold {{ $class }}">
                            {{ ucfirst($admin->role) }}
                        </span>
                    </td>


                    <td class="p-3">
                        @if($admin->is_active)
                            <span class="px-2 py-1 text-xs rounded bg-green-200 text-green-800 font-semibold">
                                ● Ativo
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-200 text-red-800 font-semibold">
                                ● Inativo
                            </span>
                        @endif
                    </td>

                    <td class="p-3 text-right space-x-2">
                        <a href="{{ route('admin.admins.show', $admin) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                            Ver
                        </a>

                        <a href="{{ route('admin.admins.edit', $admin) }}"
                           class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
                            Editar
                        </a>

                        <form action="{{ route('admin.admins.destroy', $admin) }}"
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm"
                                    onclick="return confirm('Tem certeza que deseja excluir este administrador?')">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">
                        Nenhum administrador cadastrado ainda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
