<?php

namespace App\Services\Public\Payment;

use App\Actions\Payment\CreateBoletoPaymentAction;
use App\Actions\Payment\CreatePixPaymentAction;
use App\Actions\Payment\ProcessCardPaymentAction;
use App\Exceptions\Domain\PaymentException;
use App\Models\Order;

class PaymentService
{
    public function __construct(
        private CreatePixPaymentAction $createPixPayment,
        private CreateBoletoPaymentAction $createBoletoPayment,
        private ProcessCardPaymentAction $processCardPayment,
    ) {}

    /**
     * Exibe a página de escolha do método de pagamento.
     */
    public function method(int $orderId)
    {
        $order = $this->ownedOrder($orderId);

        return view('public.payments.index', compact('order'));
    }

    /**
     * Cria uma cobrança Pix.
     */
    public function pix(int $orderId)
    {
        $order = $this->ownedOrder($orderId);

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
        $order = $this->ownedOrder($orderId);

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
    public function card(int $orderId)
    {
        $order = $this->ownedOrder($orderId);

        return $this->cardCheckout($order);
    }

    public function cardCheckout(Order $order)
    {
        try {
            $checkout = $this->processCardPayment->execute($order);
            $link = $checkout['link'] ?? null;

            if (! is_string($link) || ! $this->isTrustedAsaasUrl($link)) {
                throw new PaymentException('O provedor não retornou um link seguro para pagamento.');
            }

            return redirect()->away($link);
        } catch (PaymentException) {
            return redirect()->route('payment.error', $order->id)
                ->with('error', 'Não foi possível abrir o ambiente seguro de pagamento. Tente novamente.');
        }
    }

    private function isTrustedAsaasUrl(string $url): bool
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');

        return ($parts['scheme'] ?? null) === 'https'
            && ($host === 'asaas.com' || str_ends_with($host, '.asaas.com'));
    }

    /**
     * Página de sucesso.
     */
    public function success(int $orderId)
    {
        $order = $this->ownedOrder($orderId);

        return view('public.payments.result.success', compact('order'));
    }

    /**
     * Página de erro.
     */
    public function error(int $orderId)
    {
        $order = $this->ownedOrder($orderId);
        $reason = request()->string('reason')->toString();

        return view('public.payments.result.error', compact('order', 'reason'));
    }

    /**
     * Retorna status atual do pedido.
     */
    public function status(int $orderId)
    {
        $order = $this->ownedOrder($orderId);

        return response()->json([
            'status' => $order->status,
            'gateway_status' => $order->gateway_status,
        ]);
    }

    /**
     * Resolve um pedido somente quando ele pertence ao cliente autenticado.
     */
    private function ownedOrder(int $orderId): Order
    {
        return Order::query()
            ->where('user_id', auth()->id())
            ->findOrFail($orderId);
    }
}
