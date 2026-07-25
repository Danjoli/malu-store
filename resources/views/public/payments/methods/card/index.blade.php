@extends('layouts.payments.app')

@section('title', 'Pagamento com Cartão')

@section('content')

<div class="max-w-lg mx-auto mt-10 p-6 bg-white shadow-xl rounded-xl">

    <h1 class="text-2xl font-bold mb-2 text-center">
        Pagamento com Cartão
    </h1>

    <p class="text-center text-gray-500 mb-6">
        Pedido #{{ $order->id }}
    </p>

    {{-- Mensagem de erro --}}
    <div id="cardError"
        class="hidden mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
    </div>

    @include('public.payments.methods.card.form')

</div>

@endsection

@push('payment-scripts')

<script>
    window.CARD_PAYMENT_URL = @json( route('payment.card.process', $order->id) );
    window.CARD_SUCCESS_URL = @json( route('payment.success', $order->id) );
    window.CARD_ERROR_URL = @json( route('payment.error', $order->id) );
</script>

@vite('resources/js/payments/card/index.js')

@endpush
