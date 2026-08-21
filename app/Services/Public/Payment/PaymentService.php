<?php

namespace App\Services\Public\Payment;

use App\Actions\Payment\CreateBoletoPaymentAction;
use App\Actions\Payment\CreatePixPaymentAction;
use App\Actions\Payment\ProcessCardPaymentAction;
use App\Http\Requests\Public\Payments\ProcessCardPaymentRequest;
use App\Models\Order;

class PaymentService
{
    public function __construct(
        protected AsaasService $asaasService,
        private CreatePixPaymentAction $createPixPayment,
        private CreateBoletoPaymentAction $createBoletoPayment,
        private ProcessCardPaymentAction $processCardPayment,
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

        $result = $this->createPixPayment->execute($order);

        // Exibe a tela do Pix
        return view('public.payments.methods.pix', [
            'order' => $order,
            'payment' => $result['payment'],
            'qr_code_base64' => $result['qr_code_base64'],
            'qr_code' => $result['qr_code'],
        ]);
    }

    /**
     * Cria uma cobrança via boleto.
     */
    public function boleto(int $orderId)
    {
        $order = Order::findOrFail($orderId);

        $payment = $this->createBoletoPayment->execute($order);

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

        return view('public.payments.methods.card.index', compact('order'));
    }

    /**
     * Processa pagamento via cartão.
     */
    public function card(ProcessCardPaymentRequest $request, int $orderId)
    {
        $order = Order::findOrFail($orderId);

        try {

            $payment = $this->processCardPayment->execute($order, $request->all());

            // Retorna sucesso

            return response()->json([
                'success' => true,
                'payment' => $payment,
            ]);
        } catch (\RuntimeException $e) {

            $message = $e->getMessage();

            // Transação não autorizada
            if (str_contains($message, 'invalid_action')) {
                return response()->json([
                    'success' => false,
                    'payment_failed' => true,
                    'error_type' => 'authorization',
                    'message' => 'Transação não autorizada. Verifique os dados do cartão e tente novamente.',
                ], 422);
            }
        }
    }

    public function cardFromData(Order $order, array $cardData)
    {
        try {
            $this->processCardPayment->execute($order, $cardData);

            return redirect()->route('payment.success', $order->id);
        } catch (\RuntimeException $exception) {
            return redirect()->route('payment.error', $order->id)
                ->with('error', 'Não foi possível autorizar o cartão. Confira os dados e tente novamente.');
        }
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
