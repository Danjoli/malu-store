@extends('layouts.payments.app')

@section('title', 'Pagamento com Cartão')

@section('content')
<div class="max-w-md mx-auto mt-12 p-6 bg-white shadow-xl rounded-xl">

    <h1 class="text-2xl font-bold mb-6 text-center">
        Pagamento com Cartão
    </h1>

    <div id="cardPaymentBrick_container"></div>

</div>
@endsection

@push('payment-scripts')
<script>
    window.MP_PUBLIC_KEY = @json(env('MP_PUBLIC_KEY'));
    window.ORDER_ID = @json($order->id);
    window.ORDER_TOTAL = @json($order->total);
    window.CSRF_TOKEN = @json(csrf_token());
    window.PAYMENT_URL = @json(route('payment.card.process', $order->id));
</script>

@vite('resources/js/payments/card.js')
@endpush


