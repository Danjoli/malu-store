@extends('layouts.admin')

@section('title', 'Categorias')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Categorias</h1>

    <a href="{{ route('admin.categories.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow">
        + Nova Categoria
    </a>
</div>

<div class="bg-white shadow rounded overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="p-3">Nome</th>
                    <th class="p-3">Slug</th>
                    <th class="p-3 text-right">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse($categories as $categorie)
                <tr class="border-t hover:bg-gray-50 transition">
                    <td class="p-3 font-medium">{{ $categorie->name }}</td>

                    <td class="p-3 text-gray-600">{{ $categorie->slug }}</td>

                    <td class="p-3 text-right space-x-2">
                        <a href="{{ route('admin.categories.show', $categorie) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                            Ver
                        </a>

                        <a href="{{ route('admin.categories.edit', $categorie) }}"
                           class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
                            Editar
                        </a>

                        <form action="{{ route('admin.categories.destroy', $categorie) }}"
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
                        Nenhuma categoria cadastrado ainda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
