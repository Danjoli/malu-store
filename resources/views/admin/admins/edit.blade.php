@extends('layouts.admin')

@section('title', 'Editar Admin')

@section('content')

<div class="h-full flex items-center justify-center px-4">

    <div class="w-full max-w-3xl">

        <a href="{{ route('admin.admins.index') }}"
           class="inline-block mb-4 text-sm text-blue-600 hover:underline">
            ← Voltar para administradores
        </a>

        <h1 class="text-4xl font-bold mb-8 text-center">
            Editar Administrador
        </h1>

        <div class="bg-white p-8 rounded-lg shadow-md">

            <form action="{{ route('admin.admins.update', $admin) }}"
                  method="POST"
                  class="space-y-5">

                @csrf
                @method('PUT')

                @include('admin.admins.form')

                <div class="flex justify-between items-center pt-4">

                    <a href="{{ route('admin.admins.index') }}"
                       class="text-gray-600 hover:text-gray-900">
                        Cancelar
                    </a>

                    <button
                        class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition">
                        Atualizar
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
