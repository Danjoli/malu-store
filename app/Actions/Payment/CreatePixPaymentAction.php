<?php

namespace App\Actions\Payment;

use App\Models\Order;
use App\Services\Public\Payment\AsaasService;
use Illuminate\Support\Facades\Log;

class CreatePixPaymentAction
{
    public function __construct(private AsaasService $asaas) {}

    public function execute(Order $order): array
    {
        $payment = $this->asaas->createPixPayment($order);
        $pix = $this->asaas->getPixQrCode($payment['id']);
        $order->update(['gateway_payment_id' => $payment['id'] ?? null, 'gateway_status' => $payment['status'] ?? 'PENDING', 'status' => 'pending', 'payment_method' => 'pix', 'expires_at' => now()->addMinutes(30)]);

        Log::info('Cobrança Pix criada.', ['order_id' => $order->id, 'gateway_payment_id' => $order->gateway_payment_id, 'gateway_status' => $order->gateway_status]);

        return ['payment' => $payment, 'qr_code_base64' => $pix['encodedImage'], 'qr_code' => $pix['payload']];
    }
}
