@extends('layouts.app')

@section('title', 'Pagamento Falhou')

@section('content')

<div class="max-w-2xl mx-auto px-6 py-20 text-center">

    <div class="bg-white p-10 rounded-xl shadow">

        <div class="text-red-600 text-6xl mb-4">
            ❌
        </div>

        <h1 class="text-3xl font-bold mb-4 tracking-tight">
            Pagamento não foi concluído
        </h1>

        @php
            // Define a mensagem padrão
            $message = "Ocorreu um problema ao processar seu pagamento. Por favor, tente novamente.";

            if (!empty($reason)) {
                switch ($reason) {
                    case 'cancelled':
                        $message = "O pagamento foi cancelado pelo usuário ou pelo gateway.";
                        break;
                    case 'insufficient_funds':
                        $message = "O pagamento não foi concluído: saldo insuficiente.";
                        break;
                    case 'expired':
                        $message = "Este pagamento expirou. Por favor, gere um novo.";
                        break;
                    case 'failed':
                        $message = "O pagamento falhou por um erro inesperado. Tente novamente.";
                        break;
                }
            }
        @endphp

        <p class="text-gray-600 mb-6">
            {{ $message }}
        </p>

        <a href="/checkout"
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
           Tentar novamente
        </a>

    </div>

</div>

@endsection