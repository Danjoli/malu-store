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
        ]);

        return view('public.payments.methods.boleto', [
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
