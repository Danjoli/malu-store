@extends('layouts.admin.app')

@section('title', 'Categorias')

@section('content')
    <x-admin.page-header eyebrow="Catálogo" title="Categorias" description="Organize os produtos da sua loja.">
        <x-slot:actions><a href="{{ route('admin.categories.create') }}" class="rounded-xl bg-[#cf7184] px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-[#b85d70]">+ Nova categoria</a></x-slot:actions>
    </x-admin.page-header>

    <x-admin.table-card>
            <table class="w-full min-w-[620px] text-left text-sm">
                <thead class="bg-[#fdf8f6] text-xs font-bold uppercase tracking-wide text-[#746b68]">
                    <tr><th class="p-4">Categoria</th><th class="p-4">Slug</th><th class="p-4 text-right">Ações</th></tr>
                </thead>
                <tbody class="divide-y divide-[#f0e5e1]">
                    @forelse($categories as $categorie)
                        <tr class="transition hover:bg-[#fdf8f6]">
                            <td class="p-4 font-bold text-[#3e3532]">{{ $categorie->name }}</td>
                            <td class="p-4 font-mono text-xs text-[#625956]">{{ $categorie->slug }}</td>
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.categories.show', $categorie) }}" class="text-xs font-bold text-[#625956] hover:text-[#b85d70]">Ver</a>
                                <a href="{{ route('admin.categories.edit', $categorie) }}" class="ml-3 text-xs font-bold text-[#b85d70]">Editar</a>
                                <form action="{{ route('admin.categories.destroy', $categorie) }}" method="POST" class="ml-3 inline">@csrf @method('DELETE')<button type="submit" class="text-xs font-bold text-[#b44259]" onclick="return confirm('Tem certeza que deseja excluir esta categoria?')">Excluir</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-12 text-center text-[#746b68]">Nenhuma categoria cadastrada ainda.</td></tr>
                    @endforelse
                </tbody>
            </table>
    </x-admin.table-card>
@endsection
