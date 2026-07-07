<?php

namespace App\Services\Public\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

use App\Models\Order;
use App\Models\Cart;

class MercadoPagoWebhookService
{
    public function handleMercadoPago(Request $request)
    {
        Log::info('Webhook recebido', $request->all());

        MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));

        if ($request->type !== 'payment') {
            return;
        }

        $paymentId = $request->input('data.id');

        if (!$paymentId) {
            return;
        }

        $client = new PaymentClient();
        $payment = $client->get($paymentId);

        if (!$payment) {
            return;
        }

        $order = Order::with('items.variant')
            ->where('id', (int) $payment->external_reference)
            ->first();

        if (!$order) {
            Log::warning('Pedido não encontrado');
            return;
        }

        if ($order->status === 'paid' && $payment->status === 'approved') {
            return;
        }

        $order->update([
            'gateway_status' => $payment->status
        ]);

        $this->processPayment($order, $payment);
    }

    private function processPayment($order, $payment)
    {
        switch ($payment->status) {

            case 'approved':

                DB::transaction(function () use ($order) {

                    $order->update([
                        'status' => 'paid',
                        'paid_at' => now()
                    ]);

                    Cart::where('user_id', $order->user_id)
                        ->where('status', 'active')
                        ->update(['status' => 'converted']);

                    foreach ($order->items as $item) {

                        if (!$item->variant) continue;

                        if ($item->variant->stock < $item->quantity) continue;

                        $item->variant->decrement('stock', $item->quantity);
                    }
                });

                break;

            case 'pending':
            case 'in_process':

                $order->update(['status' => 'pending']);
                break;

            case 'rejected':
            case 'cancelled':

                $order->update(['status' => 'cancelled']);
                break;
        }
    }
}
