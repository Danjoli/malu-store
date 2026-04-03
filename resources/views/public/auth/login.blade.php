@extends('layouts.app')

@section('title', 'Login')

@section('content')

<div class="flex items-center justify-center py-20">

    <form method="POST" action="/login" class="bg-white p-8 shadow rounded w-96">

        @csrf

        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

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

        <input type="email" name="email" placeholder="Email"
            class="w-full border p-2 mb-4 rounded">

        <input type="password" name="password" placeholder="Senha"
            class="w-full border p-2 mb-4 rounded">

        <button class="w-full bg-blue-600 text-white py-2 rounded">
            Entrar
        </button>

        <p class="text-sm mt-4 text-center">
            Não tem conta?
            <a href="{{ route('register') }}" class="text-blue-600">Criar conta</a>
        </p>

    </form>

</div>

@endsection
