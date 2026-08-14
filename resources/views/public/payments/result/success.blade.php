@extends('layouts.public.app')

@section('title', 'Pedido Confirmado')

@section('content')
    <div class="mx-auto max-w-2xl px-6 py-20 text-center">
        <div class="rounded-xl bg-white p-10 shadow">
            <div class="mb-4 text-6xl text-green-600">
                ✅
            </div>

            <h1 class="mb-4 text-3xl font-bold tracking-tight">
                Pedido realizado com sucesso!
            </h1>

            <p class="mb-6 text-gray-600">
                Obrigado pela sua compra. Seu pedido foi recebido e está sendo processado.
            </p>

            <a
                href="/"
                class="rounded-lg bg-blue-600 px-6 py-3 text-white transition hover:bg-blue-700"
            >
                Voltar para a loja
            </a>
        </div>
    </div>
@endsection
