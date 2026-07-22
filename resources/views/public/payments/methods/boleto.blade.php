@extends('layouts.payments.app')

@section('title', 'Pagamento via Boleto')

@section('content')

<div class="max-w-lg mx-auto mt-12 p-6">

    <div class="bg-white shadow-lg rounded-xl p-8 text-center">

        <h2 class="text-3xl font-bold mb-2 text-yellow-600">
            Pagamento via Boleto
        </h2>

        <p class="text-gray-500 mb-6">
            Pedido #<strong>{{ $order->id }}</strong>
        </p>


        @if(!empty($boleto_url))

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-lg mb-6">

                <p class="text-gray-700 mb-4">
                    Seu boleto foi gerado com sucesso!
                </p>

                <a href="{{ $boleto_url }}"
                   target="_blank"
                   class="inline-block bg-yellow-500 text-white px-6 py-3 rounded-lg">
                    Visualizar Boleto
                </a>

            </div>


            @if(isset($expires_at))

                <p class="text-sm text-red-500 mb-6">
                    Vencimento:
                    {{ \Carbon\Carbon::parse($expires_at)->format('d/m/Y') }}
                </p>

            @endif


        @else


            <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg mb-6">

                <p class="text-red-600 font-semibold">
                    Não foi possível gerar o boleto.
                </p>

                <a href="{{ route('payment.method', $order->id) }}"
                   class="inline-block mt-4 bg-red-600 text-white px-5 py-2 rounded-lg">
                    Tentar novamente
                </a>

            </div>


        @endif


        <a href="{{ route('home') }}"
           class="inline-block mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg">
            Continuar Comprando
        </a>


    </div>

</div>

@endsection


@push('payment-scripts')

@vite('resources/js/payments/boleto.js')

@endpush
