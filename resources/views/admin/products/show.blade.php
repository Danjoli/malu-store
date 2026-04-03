@extends('layouts.admin')

@section('title', 'Detalhes do Produto')

@section('content')
<h1 class="text-3xl font-bold mb-6">Detalhes do Produto</h1>

<div class="bg-white shadow rounded-lg p-6 space-y-6">

    {{-- NOME --}}
    <div>
        <p class="text-sm text-gray-500">Nome</p>
        <p class="text-xl font-semibold">{{ $product->name }}</p>
    </div>

    {{-- SLUG --}}
    <div>
        <p class="text-sm text-gray-500">Slug</p>
        <p>{{ $product->slug }}</p>
    </div>

    {{-- CATEGORIA --}}
    <div>
        <p class="text-sm text-gray-500">Categoria</p>
        <p>{{ $product->category->name }}</p>
    </div>

    {{-- PREÇO --}}
    <div>
        <p class="text-sm text-gray-500">Preço</p>
        <p class="text-green-600 font-bold text-lg">
            R$ {{ number_format($product->price, 2, ',', '.') }}
        </p>
    </div>

    {{-- STATUS --}}
    <div>
        <p class="text-sm text-gray-500">Status</p>
        @if($product->active)
            <span class="bg-green-100 text-green-700 px-3 py-1 rounded">Ativo</span>
        @else
            <span class="bg-red-100 text-red-700 px-3 py-1 rounded">Inativo</span>
        @endif
    </div>

    {{-- DESCRIÇÃO --}}
    <div>
        <p class="text-sm text-gray-500">Descrição</p>
        <p class="text-gray-700 whitespace-pre-line">{{ $product->description }}</p>
    </div>

    {{-- IMAGENS --}}
    <div>
        <p class="text-sm text-gray-500 mb-2">Imagens</p>

        @if($product->images->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($product->images as $image)
                    <img src="{{ asset('products/' . $image->image) }}"
                         class="rounded shadow border">
                @endforeach
            </div>
        @else
            <p class="text-gray-400">Nenhuma imagem cadastrada</p>
        @endif
    </div>

    {{-- VARIAÇÕES --}}
    <div>
        <p class="text-sm text-gray-500 mb-2">Variações</p>

        @if($product->variants->count())
            <table class="w-full border rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Cor</th>
                        <th class="p-2 text-left">Tamanho</th>
                        <th class="p-2 text-left">Estoque</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->variants as $variant)
                    <tr class="border-t">
                        <td class="p-2">{{ $variant->color }}</td>
                        <td class="p-2">{{ $variant->size }}</td>
                        <td class="p-2 font-semibold">{{ $variant->stock }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- ESTOQUE TOTAL --}}
            <div class="mt-3">
                <span class="text-sm text-gray-500">Estoque total do produto:</span>
                <span class="font-bold text-lg ml-2">{{ $totalStock }}</span>
            </div>

        @else
            <p class="text-gray-400">Sem variações cadastradas</p>
        @endif
    </div>

    {{-- BOTÕES --}}
    <div class="flex justify-between items-center pt-6 border-t">
        <a href="{{ route('admin.products.index') }}"
           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Voltar
        </a>

        <a href="{{ route('admin.products.edit', $product) }}"
           class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
            Editar
        </a>
    </div>

</div>
@endsection
