@extends('layouts.admin')

@section('title', 'Produtos')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold">Produtos</h1>

    <a href="{{ route('admin.products.create') }}"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 shadow">
        + Novo Produto
    </a>
</div>

<div class="bg-white shadow rounded overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="p-3">Imagem</th>
                    <th class="p-3">Nome</th>
                    <th class="p-3">Categoria</th>
                    <th class="p-3">Preço</th>
                    <th class="p-3">Estoque</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse($products as $product)
                <tr class="border-t hover:bg-gray-50 transition">

                    {{-- IMAGEM --}}
                    <td class="p-3">
                        @if($product->images && $product->images->count())
                            <img src="{{ asset('products/' . $product->images->first()->image) }}"
                                 class="w-14 h-14 object-cover rounded border shadow">
                        @else
                            <div class="w-14 h-14 flex items-center justify-center bg-gray-100 text-gray-400 rounded border text-xs">
                                Sem imagem
                            </div>
                        @endif
                    </td>

                    <td class="p-3 font-semibold">{{ $product->name }}</td>

                    <td class="p-3">
                        {{ $product->category->name ?? 'Sem categoria' }}
                    </td>

                    <td class="p-3">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </td>

                    <td class="p-3">
                        {{ optional($product->variants)->sum('stock') ?? 0 }}
                    </td>

                    <td class="p-3">
                        @if($product->active)
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
                        <a href="{{ route('admin.products.show', $product) }}"
                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">
                            Ver
                        </a>

                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 text-sm">
                            Editar
                        </a>

                        <form action="{{ route('admin.products.destroy', $product) }}"
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm"
                                    onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                                Excluir
                            </button>
                        </form>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">
                        Nenhum produto cadastrado ainda.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
