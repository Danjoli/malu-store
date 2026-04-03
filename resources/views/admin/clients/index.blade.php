@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Clientes</h1>
</div>

<div class="bg-white shadow rounded overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="p-3">Nome</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Senha</th>
                    <th class="p-3">Telêfone</th>
                    <th class="p-3 text-right">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $user)
                <tr class="border-t hover:bg-gray-50 transition">
                    <td class="p-3 font-medium">{{ $user->name }}</td>

                    <td class="p-3 text-gray-600">{{ $user->email }}</td>

                    <td class="p-3 font-medium">{{ $user->password }}</td>

                    <td class="p-3 text-gray-600">{{ $user->phone }}</td>


                    <td class="p-3 text-right space-x-2">
                        <a href="{{ route('admin.clients.show', $user) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                            Ver
                        </a>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">
                        Nenhum cliente cadastrado ainda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
