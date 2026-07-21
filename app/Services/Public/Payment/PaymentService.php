<?php

namespace App\Services\Public\Payment;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        protected AsaasService $asaasService
    ) {}

    /**
     * Exibe a página de escolha do método de pagamento.
     */
    public function method(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        return view('public.payments.index', compact('order'));
    }

    /**
     * Cria uma cobrança Pix.
     */
    public function pix(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        $payment = $this->asaasService->createPixPayment($order);

        $order->update([
            'gateway_payment_id' => $payment['id'] ?? null,
            'gateway_status' => $payment['status'] ?? 'PENDING',
            'status' => 'pending',
        ]);

        return view('payment.pix', [
            'order' => $order,
            'payment' => $payment,
        ]);
    }

    /**
     * Cria uma cobrança via boleto.
     */
    public function boleto(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        $payment = $this->asaasService->createBoletoPayment($order);

        $order->update([
            'gateway_payment_id' => $payment['id'] ?? null,
            'gateway_status' => $payment['status'] ?? 'PENDING',
            'status' => 'pending',
        ]);

        return view('payment.boleto', [
            'order' => $order,
            'payment' => $payment,
        ]);
    }

    /**
     * Exibe a página de pagamento via cartão.
     */
    public function cardView(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        return view('public.payments.card', compact('order'));
    }

    /**
     * Processa pagamento via cartão.
     */
    public function card(Request $request, int $orderId)
    {
        $order = Order::findOrFail($orderId);

        $request->validate([
            'card_number' => ['required'],
            'holder_name' => ['required'],
            'expiration_month' => ['required'],
            'expiration_year' => ['required'],
            'ccv' => ['required'],
        ]);

        $payment = $this->asaasService->createCardPayment(
            $order,
            $request->all()
        );

        $order->update([
            'gateway_payment_id' => $payment['id'] ?? null,
            'gateway_status' => $payment['status'] ?? 'PENDING',
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'payment' => $payment,
        ]);
    }

    /**
     * Página de sucesso.
     */
    public function success(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        return view('payment.success', compact('order'));
    }

    /**
     * Página de erro.
     */
    public function error(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        return view('payment.error', compact('order'));
    }
}
