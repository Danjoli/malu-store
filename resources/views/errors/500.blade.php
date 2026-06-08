@extends('layouts.public.app')

@section('title', 'Erro interno')

@section('content')
<div class="container mx-auto px-4 py-20 text-center">

    <h1 class="text-8xl font-bold text-orange-500">
        500
    </h1>

    <h2 class="text-3xl font-semibold mt-4">
        Ocorreu um erro inesperado
    </h2>

    <p class="text-gray-600 mt-4">
        Estamos trabalhando para resolver o problema.
        Tente novamente em alguns instantes.
    </p>

    <a href="{{ route('home') }}"
       class="inline-block mt-8 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Voltar para a loja
    </a>

</div>
@endsection
