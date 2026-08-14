@extends('layouts.admin.app')

@section('title', 'Detalhes do Produto')

@section('content')
    <h1 class="mb-6 text-3xl font-bold">
        Detalhes do Produto
    </h1>

    <div class="space-y-6 rounded-lg bg-white p-6 shadow">
        {{-- Nome --}}
        <div>
            <p class="text-sm text-gray-500">
                Nome
            </p>

            <p class="text-xl font-semibold">
                {{ $product->name }}
            </p>
        </div>

        {{-- Slug --}}
        <div>
            <p class="text-sm text-gray-500">
                Slug
            </p>

            <p>
                {{ $product->slug }}
            </p>
        </div>

        {{-- Categoria --}}
        <div>
            <p class="text-sm text-gray-500">
                Categoria
            </p>

            <p>
                {{ $product->category->name }}
            </p>
        </div>

        {{-- Preço --}}
        <div>
            <p class="text-sm text-gray-500">
                Preço
            </p>

            <p class="text-lg font-bold text-green-600">
                R$ {{ number_format($product->price, 2, ',', '.') }}
            </p>
        </div>

        {{-- Status --}}
        <div>
            <p class="text-sm text-gray-500">
                Status
            </p>

            @if ($product->active)
                <span class="rounded bg-green-100 px-3 py-1 text-green-700">
                    Ativo
                </span>
            @else
                <span class="rounded bg-red-100 px-3 py-1 text-red-700">
                    Inativo
                </span>
            @endif
        </div>

        {{-- Descrição --}}
        <div>
            <p class="text-sm text-gray-500">
                Descrição
            </p>

            <p class="whitespace-pre-line text-gray-700">
                {{ $product->description }}
            </p>
        </div>

        {{-- Imagens --}}
        <div>
            <p class="mb-2 text-sm text-gray-500">
                Imagens
            </p>

            @if ($product->images->count())
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    @foreach ($product->images as $image)
                        <img
                            src="{{ asset('storage/products/' . $image->image) }}"
                            alt="{{ $product->name }}"
                            class="rounded border shadow"
                        >
                    @endforeach
                </div>
            @else
                <p class="text-gray-400">
                    Nenhuma imagem cadastrada
                </p>
            @endif
        </div>

        {{-- Variações --}}
        <div>
            <p class="mb-2 text-sm text-gray-500">
                Variações
            </p>

            @if ($product->variants->count())
                <table class="w-full rounded border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Cor</th>
                            <th class="p-2 text-left">Tamanho</th>
                            <th class="p-2 text-left">Estoque</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($product->variants as $variant)
                            <tr class="border-t">
                                <td class="p-2">
                                    {{ $variant->color }}
                                </td>

                                <td class="p-2">
                                    {{ $variant->size }}
                                </td>

                                <td class="p-2 font-semibold">
                                    {{ $variant->stock }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3">
                    <span class="text-sm text-gray-500">
                        Estoque total do produto:
                    </span>

                    <span class="ml-2 text-lg font-bold">
                        {{ $totalStock }}
                    </span>
                </div>
            @else
                <p class="text-gray-400">
                    Sem variações cadastradas
                </p>
            @endif
        </div>

        {{-- Botões --}}
        <div class="flex items-center justify-between border-t pt-6">
            <a
                href="{{ route('admin.products.index') }}"
                class="rounded bg-gray-600 px-4 py-2 text-white hover:bg-gray-700"
            >
                Voltar
            </a>

            <a
                href="{{ route('admin.products.edit', $product) }}"
                class="rounded bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600"
            >
                Editar
            </a>
        </div>
    </div>
@endsection
