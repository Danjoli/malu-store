@extends('layouts.public.app')

@section('title', 'Checkout')

@section('content')

<div class="store-container py-10 md:py-14">

    <h1 class="store-title mb-8 text-4xl md:text-5xl">
        Finalizar Compra
    </h1>

    <form
        id="checkout-form"
        action="{{ route('checkout.process') }}"
        method="POST"
    >
        @csrf

        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">

            {{-- FORMULÁRIO DE ENTREGA --}}
            @include('public.checkout.address-form')

            {{-- RESUMO DO PEDIDO --}}
            @include('public.checkout.order-summary')

        </div>

    </form>

</div>

@endsection

@push('scripts')

<script>
    window.SUBTOTAL = @json($subtotal ?? 0);
    window.CSRF_TOKEN = @json(csrf_token());
</script>

@endpush
