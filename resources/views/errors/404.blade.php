@extends('layouts.public.app')

@section('title', 'Página não encontrada')

@section('content')
<div class="container mx-auto px-4 py-20 text-center">

    <h1 class="text-8xl font-bold text-blue-600">
        404
    </h1>

    <h2 class="text-3xl font-semibold mt-4">
        Página não encontrada
    </h2>

    <p class="text-gray-600 mt-4">
        A página que você está procurando não existe ou foi removida.
    </p>

    <a href="{{ route('home') }}"
       class="inline-block mt-8 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Voltar para a loja
    </a>

</div>
@endsection
