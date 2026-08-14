@extends('layouts.payments.app')

@section('title', 'Pagamento com Cartão')

@section('content')
    <div class="w-full max-w-lg rounded-2xl border border-[#eaded9] bg-white p-6 shadow-[0_12px_34px_rgba(76,50,47,0.08)] sm:p-8">
        <p class="text-center text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Pedido #{{ $order->id }}</p>
        <h1 class="mt-2 text-center font-['Cormorant_Garamond'] text-3xl font-semibold">Pagamento com cartão</h1>
        <p class="mt-2 text-center text-sm text-[#746b68]">Insira os dados do titular do cartão.</p>
        <div id="cardError" class="hidden mb-4 mt-6 rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] p-4 text-sm text-[#b44259]"></div>
        <div class="mt-6">@include('public.payments.methods.card.form')</div>
    </div>
@endsection
