@extends('layouts.payments.app')

@section('title', 'Pagamento via Boleto')

@section('content')
    <div class="w-full max-w-lg rounded-2xl border border-[#eaded9] bg-white p-6 text-center shadow-[0_12px_34px_rgba(76,50,47,0.08)] sm:p-8"><p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Pedido #{{ $order->id }}</p><h1 class="mt-2 font-['Cormorant_Garamond'] text-3xl font-semibold">Pagamento via boleto</h1>
        @if(!empty($boleto_url))<p class="mt-3 text-sm text-[#746b68]">Seu boleto foi gerado. Faça o pagamento até o vencimento.</p><div class="my-6 rounded-2xl bg-[#fff6df] p-5 text-[#986d16]"><p class="text-sm font-semibold">Boleto disponível</p>@if(isset($expires_at))<p class="mt-2 text-sm">Vencimento: <strong>{{ \Carbon\Carbon::parse($expires_at)->format('d/m/Y') }}</strong></p>@endif</div><a href="{{ $boleto_url }}" target="_blank" class="inline-flex w-full items-center justify-center rounded-xl bg-[#cf7184] px-5 py-3.5 text-sm font-bold text-white hover:bg-[#b85d70]">Visualizar boleto</a>@else<div class="my-6 rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] p-5 text-sm text-[#b44259]">Não foi possível gerar o boleto.</div><a href="{{ route('payment.method', $order->id) }}" class="inline-flex w-full items-center justify-center rounded-xl bg-[#cf7184] px-5 py-3.5 text-sm font-bold text-white hover:bg-[#b85d70]">Tentar novamente</a>@endif
        <a href="{{ route('home') }}" class="mt-4 inline-block text-sm font-semibold text-[#746b68] hover:text-[#b85d70]">Continuar comprando</a>
    </div>
@endsection

@push('payment-scripts')
<script>window.BOLETO_ORDER_ID=@json($order->id);window.BOLETO_STATUS_URL=@json(route('payment.status',$order->id));window.BOLETO_SUCCESS_URL=@json(route('payment.success',$order->id));window.BOLETO_ERROR_URL=@json(route('payment.error',$order->id));</script>
@vite('resources/js/payments/boleto/index.js')
@endpush
