@extends('layouts.public.app')

@section('title', 'Checkout')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

    <h1 class="text-3xl font-bold mb-8 tracking-tight">
        Finalizar Compra
    </h1>

    <form
        id="checkout-form"
        action="{{ route('checkout.process') }}"
        method="POST"
    >
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

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
