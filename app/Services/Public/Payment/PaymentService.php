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

        // Cria o pagamento
        $payment = $this->asaasService->createPixPayment($order);

        // Busca o QR Code do Pix
        $pix = $this->asaasService->getPixQrCode($payment['id']);

        // Atualiza o pedido
        $order->update([
            'gateway_payment_id' => $payment['id'] ?? null,
            'gateway_status' => $payment['status'] ?? 'PENDING',
            'status' => 'pending',
            'payment_method' => 'pix',
            'expires_at' => now()->addMinutes(30),
        ]);

        // Exibe a tela do Pix
        return view('public.payments.methods.pix', [
            'order' => $order,
            'payment' => $payment,
            'qr_code_base64' => $pix['encodedImage'],
            'qr_code' => $pix['payload'],
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
            'payment_method' => 'boleto',
            'expires_at' => isset($payment['dueDate'])
                ? $payment['dueDate'] . ' 23:59:59'
                : null,
        ]);

        return view('public.payments.methods.boleto', [
            'order' => $order,
            'payment' => $payment,

            // Link direto para o PDF do boleto
            'boleto_url' => $payment['bankSlipUrl']
                ?? null,

            // Link da fatura do Asaas, como alternativa
            'invoice_url' => $payment['invoiceUrl']
                ?? null,

            'expires_at' => $payment['dueDate']
                ?? null,
        ]);
    }

    /**
     * Exibe a página de pagamento via cartão.
     */
    public function cardView(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        return view('public.payments.methods.card', compact('order'));
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
            'cpf' => ['required'],
            'expiration_month' => ['required'],
            'expiration_year' => ['required'],
            'ccv' => ['required'],
        ]);

        $payment = $this->asaasService->createCardPayment(
            $order,
            $request->all()
        );

        $paymentStatus = $payment['status'] ?? 'PENDING';

        $order->update([
            'gateway_payment_id' => $payment['id'] ?? null,
            'gateway_status' => $paymentStatus,
            'status' => $paymentStatus === 'CONFIRMED'
                ? 'paid'
                : 'pending',
            'payment_method' => 'card',
            'paid_at' => $paymentStatus === 'CONFIRMED'
                ? now()
                : null,
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

        return view('public.payments.result.success', compact('order'));
    }

    /**
     * Página de erro.
     */
    public function error(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        return view('public.payments.result.error', compact('order'));
    }

    /**
     * Retorna status atual do pedido.
     */
    public function status(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        return response()->json([
            'status' => $order->status,
            'gateway_status' => $order->gateway_status,
        ]);
    }
}
