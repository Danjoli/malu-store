@extends('layouts.admin')

@section('title', 'Criar Categoria')

@section('content')


<div class="h-full flex items-center justify-center px-4">
    <div class="w-full max-w-3xl">
        <a href="{{ route('admin.categories.index') }}"
        class="inline-block mb-4 text-sm text-blue-600 hover:underline">
            ← Voltar para categorias
        </a>

        <h1 class="text-3xl font-bold mb-6">Nova Categoria</h1>

        <div class="bg-white p-6 rounded shadow max-w-xl">
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @include('admin.categories.form')

                <div class="flex justify-between items-center pt-2">
                    <a href="{{ route('admin.categories.index') }}"
                    class="text-gray-600 hover:text-gray-900">
                        Cancelar
                    </a>

                    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
