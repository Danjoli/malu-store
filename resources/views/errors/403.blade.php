@extends('layouts.public.app')

@section('title', 'Acesso negado')

@section('content')
<div class="container mx-auto px-4 py-20 text-center">

    <h1 class="text-8xl font-bold text-red-600">
        403
    </h1>

    <h2 class="text-3xl font-semibold mt-4">
        Acesso negado
    </h2>

    <p class="text-gray-600 mt-4">
        Você não possui permissão para acessar esta página.
    </p>

    <a href="{{ url()->previous() }}"
       class="inline-block mt-8 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Voltar
    </a>

</div>
@endsection
