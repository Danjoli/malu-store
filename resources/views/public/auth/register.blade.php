@extends('layouts.app')

@section('title', 'Criar Conta')

@section('content')

<div class="flex items-center justify-center py-20">

    <form method="POST" action="/register" class="bg-white p-8 shadow rounded w-96">

        @csrf

        <h2 class="text-2xl font-bold mb-6 text-center">Criar Conta</h2>

        {{-- ERROS --}}
        @if ($errors->any())

            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">

                <ul class="list-disc pl-5">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif

        <input type="text" name="name" placeholder="Nome"
            class="w-full border p-2 mb-4 rounded">

        <input type="email" name="email" placeholder="Email"
            class="w-full border p-2 mb-4 rounded">

        <input type="text" name="phone" placeholder="Telefone (11999999999)"
            class="w-full border p-2 mb-3 rounded focus:outline-green-500">

        <input type="password" name="password" placeholder="Senha"
            class="w-full border p-2 mb-4 rounded">

        <button class="w-full bg-blue-600 text-white py-2 rounded">
            Criar Conta
        </button>

        <p class="text-sm mt-4 text-center">
            Já tem conta?
            <a href="{{ route('login') }}" class="text-blue-600">Login</a>
        </p>

    </form>

</div>

@endsection
