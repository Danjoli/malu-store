@extends('layouts.app')

@section('title', 'Pedido Confirmado')

@section('content')

<div class="max-w-2xl mx-auto px-6 py-20 text-center">

    <div class="bg-white p-10 rounded-xl shadow">

        <div class="text-green-600 text-6xl mb-4">
            ✅
        </div>

        <h1 class="text-3xl font-bold mb-4 tracking-tight">
            Pedido realizado com sucesso!
        </h1>

        <p class="text-gray-600 mb-6">
            Obrigado pela sua compra. Seu pedido foi recebido e está sendo processado.
        </p>

        <a href="/"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
           Voltar para a loja
        </a>

    </div>

</div>

@endsection
