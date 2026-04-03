<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

use App\Models\Order;
use App\Models\Cart;

class WebhookController extends Controller
{
    public function mercadopago(Request $request)
    {
        \Log::info('Webhook recebido', $request->all());

        try {

            MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));

            if ($request->type !== 'payment') {
                return response()->json(['status' => 'ignored'], 200);
            }

            $paymentId = $request->input('data.id');

            if (!$paymentId) {
                return response()->json(['status' => 'no_payment_id'], 200);
            }

            $client = new PaymentClient();
            $payment = $client->get($paymentId);

            if (!$payment || !isset($payment->id)) {
                return response()->json(['status' => 'payment_not_found'], 200);
            }

            \Log::info('Pagamento MP', [
                'id' => $payment->id,
                'status' => $payment->status,
                'external_reference' => $payment->external_reference
            ]);

            /*
            |--------------------------------------------------------------------------
            | Buscar pedido (CORRIGIDO)
            |--------------------------------------------------------------------------
            */
            $order = Order::with('items.variant')
                ->where('id', (int) $payment->external_reference)
                ->first();

            if (!$order) {
                \Log::warning('Pedido não encontrado', [
                    'payment_id' => $payment->id,
                    'external_reference' => $payment->external_reference
                ]);

                return response()->json(['status' => 'order_not_found'], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Evitar duplicidade
            |--------------------------------------------------------------------------
            */
            if ($order->status === 'paid' && $payment->status === 'approved') {
                return response()->json(['status' => 'already_processed'], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Atualiza status do gateway
            |--------------------------------------------------------------------------
            */
            $order->update([
                'gateway_status' => $payment->status
            ]);

            /*
            |--------------------------------------------------------------------------
            | STATUS DO PAGAMENTO
            |--------------------------------------------------------------------------
            */
            switch ($payment->status) {

                case 'approved':

                    DB::transaction(function () use ($order) {

                        \Log::info('PAGAMENTO APROVADO', [
                            'order_id' => $order->id
                        ]);

                        // Marca pedido como pago
                        $order->update([
                            'status' => 'paid',
                            'paid_at' => now()
                        ]);

                        // CONVERTE CARRINHO (SEU OBJETIVO)
                        Cart::where('user_id', $order->user_id)
                            ->where('status', 'active')
                            ->update(['status' => 'converted']);

                        // Atualiza estoque
                        foreach ($order->items as $item) {

                            if (!$item->variant) {
                                \Log::warning('Item sem variante', [
                                    'order_id' => $order->id
                                ]);
                                continue;
                            }

                            if ($item->variant->stock < $item->quantity) {
                                \Log::warning('Estoque insuficiente', [
                                    'order_id' => $order->id,
                                    'variant_id' => $item->variant->id
                                ]);
                                continue;
                            }

                            $item->variant->decrement('stock', $item->quantity);
                        }

                    });

                    break;

                case 'pending':
                case 'in_process':

                    $order->update([
                        'status' => 'pending'
                    ]);

                    break;

                case 'rejected':
                case 'cancelled':

                    $order->update([
                        'status' => 'cancelled'
                    ]);

                    break;
            }

            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {

            \Log::error('Erro webhook', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'status' => 'error_but_received'
            ], 200);
        }
    }
}