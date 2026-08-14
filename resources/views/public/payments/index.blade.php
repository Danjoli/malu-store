@extends('layouts.public.app')

@section('title', 'Pagamento')

@section('content')
    <div class="mx-auto max-w-3xl px-5 py-10 sm:py-14">
        <div class="mb-7"><p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Finalização</p><h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold">Como deseja pagar?</h1><p class="mt-2 text-sm text-[#746b68]">Pedido #{{ $order->id }} · Total: <strong class="text-[#2d2928]">R$ {{ number_format($order->total, 2, ',', '.') }}</strong></p></div>
        <form action="{{ route('payment.process', $order->id) }}" method="POST" class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)] sm:p-7">@csrf
            <div class="space-y-3">
                <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-[#eaded9] p-4 transition hover:border-[#cf7184] hover:bg-[#fdf8f6]"><input type="radio" name="payment_method" value="card" class="h-4 w-4 accent-[#cf7184]" required><span class="flex-1"><span class="block text-sm font-bold">Cartão de crédito</span><span class="mt-1 block text-xs text-[#746b68]">Pagamento rápido e seguro</span></span><span class="text-xl">💳</span></label>
                <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-[#eaded9] p-4 transition hover:border-[#cf7184] hover:bg-[#fdf8f6]"><input type="radio" name="payment_method" value="pix" class="h-4 w-4 accent-[#cf7184]"><span class="flex-1"><span class="block text-sm font-bold">Pix</span><span class="mt-1 block text-xs text-[#746b68]">Confirmação em poucos instantes</span></span><span class="text-xl">⚡</span></label>
                <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-[#eaded9] p-4 transition hover:border-[#cf7184] hover:bg-[#fdf8f6]"><input type="radio" name="payment_method" value="boleto" class="h-4 w-4 accent-[#cf7184]"><span class="flex-1"><span class="block text-sm font-bold">Boleto bancário</span><span class="mt-1 block text-xs text-[#746b68]">Compensação conforme o banco</span></span><span class="text-xl">▤</span></label>
            </div>
            <button type="submit" class="mt-6 w-full rounded-xl bg-[#cf7184] py-3.5 text-sm font-bold text-white transition hover:bg-[#b85d70]">Continuar para pagamento</button>
        </form>
    </div>
@endsection
