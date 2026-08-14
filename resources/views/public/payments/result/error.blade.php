@extends('layouts.public.app')

@section('title', 'Pagamento não concluído')

@section('content')
    @php
        $messages = [
            'cancelled' => 'O pagamento foi cancelado. Você pode escolher outra forma de pagamento.',
            'insufficient_funds' => 'Não foi possível autorizar o pagamento por saldo insuficiente.',
            'expired' => 'O prazo deste pagamento expirou. Gere uma nova cobrança para continuar.',
            'card_declined' => 'A operadora não autorizou a transação. Revise os dados ou use outro meio de pagamento.',
            'failed' => 'O pagamento não foi concluído. Tente novamente em alguns instantes.',
        ];

        $message = $messages[$reason ?? '']
            ?? 'Ocorreu um problema ao processar o pagamento. Tente novamente.';
    @endphp

    <x-error-page
        code="!"
        eyebrow="Pagamento não concluído"
        title="Vamos tentar de novo?"
        :message="$message"
        action-label="Escolher outra forma"
        :action-url="route('payment.method', $order)"
    />
@endsection
