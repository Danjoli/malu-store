<?php

namespace App\UseCases\Payment;

use App\Data\Payments\PixPaymentResult;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OperationalAlertService;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreatePixPayment
{
    public function __construct(
        private AsaasService $asaas,
        private OperationalAlertService $alerts,
    ) {}

    public function execute(Order $order): PixPaymentResult
    {
        try {
            $payment = $this->asaas->createPixPayment($order);
            $pix = $this->asaas->getPixQrCode($payment['id']);

            $order->update([
                'gateway_payment_id' => $payment['id'] ?? null,
                'gateway_status' => $payment['status'] ?? 'PENDING',
                'status' => OrderStatus::Pending->value,
                'payment_method' => 'pix',
                'expires_at' => now()->addMinutes(30),
            ]);

            Log::info('Cobrança Pix criada.', [
                'order_id' => $order->id,
                'gateway_payment_id' => $order->gateway_payment_id,
                'gateway_status' => $order->gateway_status,
            ]);

            return new PixPaymentResult(
                payment: $payment,
                qrCodeBase64: $pix['encodedImage'],
                qrCode: $pix['payload'],
            );
        } catch (Throwable $exception) {
            $this->alerts->critical('Falha ao criar cobrança Pix.', [
                'Pedido' => $order->id,
                'Tipo de erro' => $exception::class,
            ]);

            throw $exception;
        }
    }
}
