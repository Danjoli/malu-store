<?php

namespace App\Actions\Payment;

use App\Models\Order;
use App\Services\OperationalAlertService;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreatePixPaymentAction
{
    public function __construct(
        private AsaasService $asaas,
        private OperationalAlertService $alerts,
    ) {}

    public function execute(Order $order): array
    {
        try {
            $payment = $this->asaas->createPixPayment($order);
            $pix = $this->asaas->getPixQrCode($payment['id']);

            // O pedido mantém a referência da cobrança, sem persistir QR Code ou payload Pix.
            $order->update([
                'gateway_payment_id' => $payment['id'] ?? null,
                'gateway_status' => $payment['status'] ?? 'PENDING',
                'status' => 'pending',
                'payment_method' => 'pix',
                'expires_at' => now()->addMinutes(30),
            ]);

            Log::info('Cobrança Pix criada.', [
                'order_id' => $order->id,
                'gateway_payment_id' => $order->gateway_payment_id,
                'gateway_status' => $order->gateway_status,
            ]);

            return [
                'payment' => $payment,
                'qr_code_base64' => $pix['encodedImage'],
                'qr_code' => $pix['payload'],
            ];
        } catch (Throwable $exception) {
            $this->alerts->critical('Falha ao criar cobrança Pix.', [
                'Pedido' => $order->id,
                'Tipo de erro' => $exception::class,
            ]);

            throw $exception;
        }
    }
}
