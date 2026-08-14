@extends('layouts.admin.app')

@section('title', 'Produtos')

@section('content')
    <x-admin.page-header
        eyebrow="Catálogo"
        title="Produtos"
        description="Gerencie os itens da sua loja."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.products.create') }}"
                class="rounded-xl bg-[#cf7184] px-4 py-3 text-center text-sm font-bold text-white transition hover:bg-[#b85d70]"
            >
                + Novo produto
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table-card>
        <table class="w-full min-w-[850px] text-left text-sm">
            <thead class="bg-[#fdf8f6] text-xs font-bold uppercase tracking-wide text-[#746b68]">
                <tr>
                    <th class="p-4">Imagem</th>
                    <th class="p-4">Produto</th>
                    <th class="p-4">Categoria</th>
                    <th class="p-4">Preço</th>
                    <th class="p-4">Estoque</th>
                    <th class="p-4">Status</th>
                    <th class="p-4 text-right">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-[#f0e5e1]">
                @forelse ($products as $product)
                    <tr class="transition hover:bg-[#fdf8f6]">
                        <td class="p-4">
                            @if ($product->images && $product->images->count())
                                <img
                                    src="{{ asset('storage/products/' . $product->images->first()->image) }}"
                                    alt="{{ $product->name }}"
                                    class="h-14 w-12 rounded-lg border border-[#eaded9] object-cover"
                                >
                            @else
                                <div class="flex h-14 w-12 items-center justify-center rounded-lg bg-[#f8f3f1] text-[10px] text-[#857b78]">
                                    Sem foto
                                </div>
                            @endif
                        </td>

                        <td class="p-4 font-bold text-[#3e3532]">
                            {{ $product->name }}
                        </td>

                        <td class="p-4 text-[#625956]">
                            {{ $product->category->name ?? 'Sem categoria' }}
                        </td>

                        <td class="p-4 font-semibold">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </td>

                        <td class="p-4 text-[#625956]">
                            {{ optional($product->variants)->sum('stock') ?? 0 }}
                        </td>

                        <td class="p-4">
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $product->active ? 'bg-[#eaf6ef] text-[#27754a]' : 'bg-[#fdf0f3] text-[#b44259]' }}">
                                {{ $product->active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>

                        <td class="p-4 text-right">
                            <a
                                href="{{ route('admin.products.show', $product) }}"
                                class="text-xs font-bold text-[#625956] hover:text-[#b85d70]"
                            >
                                Ver
                            </a>

                            <a
                                href="{{ route('admin.products.edit', $product) }}"
                                class="ml-3 text-xs font-bold text-[#b85d70]"
                            >
                                Editar
                            </a>

                            <form
                                action="{{ route('admin.products.destroy', $product) }}"
                                method="POST"
                                class="ml-3 inline"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-xs font-bold text-[#b44259]"
                                    onclick="return confirm('Tem certeza que deseja excluir este produto?')"
                                >
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-[#746b68]">
                            Nenhum produto cadastrado ainda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-admin.table-card>
@endsection
