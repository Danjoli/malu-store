@extends('layouts.public.app')

@section('title', 'Checkout')

@section('content')
    <div class="store-container max-w-7xl py-10 md:py-14">
        <p class="store-kicker text-[#bd5564]">Checkout seguro</p>
        <h1 class="store-title mt-2 text-4xl md:text-5xl">Finalizar compra</h1>
        <p class="mt-2 max-w-2xl text-sm text-stone-500">Revise o endereço, escolha a entrega e defina o pagamento antes de confirmar.</p>

        <form
            id="checkout-form"
            action="{{ route('checkout.process') }}"
            method="POST"
        >
            @csrf

            <div class="mt-8 grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_22rem]">
                <div class="space-y-5">
                    <x-public.checkout.address-section :addresses="$addresses" :address="$address" />
                    <x-public.checkout.customer-section :address="$address" />
                    <x-public.checkout.shipping-section />
                    <x-public.checkout.payment-section />
                    <x-public.checkout.review-items-section :items="$items" :subtotal="$subtotal" />
                </div>
                @include('public.checkout.order-summary')
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        window.SUBTOTAL = @json($subtotal ?? 0);
        window.CSRF_TOKEN = @json(csrf_token());
        window.CHECKOUT_ADDRESSES = @json($addresses ?? []);
    </script>
@endpush
