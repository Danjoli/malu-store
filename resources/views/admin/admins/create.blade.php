@extends('layouts.admin')

@section('title', 'Criar Admin')

@section('content')

<div class="h-full flex items-center justify-center px-4">

    <div class="w-full max-w-3xl">

        <a href="{{ route('admin.admins.index') }}"
           class="inline-block mb-4 text-sm text-blue-600 hover:underline">
            ← Voltar para administradores
        </a>

        <h1 class="text-4xl font-bold mb-8 text-center">
            Novo Administrador
        </h1>

        <div class="bg-white p-8 rounded-lg shadow-md">

            <form action="{{ route('admin.admins.store') }}" method="POST" class="space-y-5">

                @include('admin.admins.form')

                <div class="flex justify-between items-center pt-4">

                    <a href="{{ route('admin.admins.index') }}"
                       class="text-gray-600 hover:text-gray-900">
                        Cancelar
                    </a>

                    <button
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                        Salvar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
