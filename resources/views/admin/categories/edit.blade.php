@extends('layouts.admin')

@section('title', 'Editar Categoria')

@section('content')

<div class="h-full flex items-center justify-center px-4">
    <div class="w-full max-w-3xl">
        <a href="{{ route('admin.categories.index') }}"
        class="inline-block mb-4 text-sm text-blue-600 hover:underline">
            ← Voltar para categorias
        </a>

        <h1 class="text-3xl font-bold mb-6">Editar Categoria</h1>

        <div class="bg-white p-6 rounded shadow max-w-xl">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-4">
                @method('PUT')
                @include('admin.categories.form')

                <div class="flex justify-between items-center pt-2">
                    <a href="{{ route('admin.categories.index') }}"
                    class="text-gray-600 hover:text-gray-900">
                        Cancelar
                    </a>

                    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
