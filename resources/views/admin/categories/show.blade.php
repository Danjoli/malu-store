@extends('layouts.admin')

@section('title', 'Detalhes da Categoria')

@section('content')

<div class="h-full flex items-center justify-center px-4">
    <div class="w-full max-w-3xl">
        <h1 class="text-3xl font-bold mb-6">Detalhes do Categoria</h1>

        <div class="bg-white shadow rounded-lg p-6 max-w-2xl space-y-6">

            {{-- ID --}}
            <div>
                <p class="text-sm text-gray-500">ID</p>
                <p class="text-lg font-semibold">{{ $category->id }}</p>
            </div>

            {{-- Nome --}}
            <div>
                <p class="text-sm text-gray-500">Nome</p>
                <p class="text-lg font-semibold">{{ $category->name }}</p>
            </div>

            {{-- Slug --}}
            <div>
                <p class="text-sm text-gray-500">Slug</p>
                <p class="text-lg font-semibold">{{ $category->slug }}</p>
            </div>

            {{-- Datas --}}
            <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                <div>
                    <p class="text-sm text-gray-500">Criado em</p>
                    <p class="font-medium">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Última atualização</p>
                    <p class="font-medium">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            {{-- BOTÕES --}}
            <div class="flex justify-between items-center pt-6 border-t">
                <a href="{{ route('admin.categories.index') }}"
                class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Voltar
                </a>

                <a href="{{ route('admin.categories.edit', $category) }}"
                class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                    Editar
                </a>
            </div>
        </div>
        
    </div>
</div>
@endsection
