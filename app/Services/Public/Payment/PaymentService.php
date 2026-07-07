<?php

namespace App\Services\Public\Payment;

use App\Models\{Order, Cart};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class PaymentService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
    }

    /*
    |-------------------------
    | PIX
    |-------------------------
    */
    public function pix(int $orderId)
    {
        $order = Order::with('user')->findOrFail($orderId);

        $this->checkExpiration($order);

        try {

            $client = new PaymentClient();

            $payment = $client->create([
                "transaction_amount" => (float) $order->total,
                "description" => "Pedido #" . $order->id,
                "payment_method_id" => "pix",
                "notification_url" => route('api.webhooks.mercado-pago'),
                "external_reference" => (string) $order->id,
                "payer" => [
                    "email" => $order->user->email
                ]
            ]);

            $expiresAt = now()->addMinutes(30);

            $order->update([
                'gateway_payment_id' => $payment->id,
                'expires_at' => $expiresAt,
                'status' => 'pending',
                'gateway_status' => 'pending'
            ]);

            return view('public.payments.methods.pix', [
                'order' => $order,
                'expires_at' => $expiresAt,
                'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
                'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64
            ]);
        } catch (MPApiException $e) {

            dd([
                'mercadopago_error' => $e->getApiResponse()->getContent()
            ]);
        }
    }

    /*
    |-------------------------
    | BOLETO
    |-------------------------
    */
    public function boleto(int $orderId)
    {
        $order = Order::with(['user', 'address', 'user.addresses'])->findOrFail($orderId);

        $this->checkExpiration($order);

        $address = $order->address ?? $order->user->addresses->first();

        if (!$address) {
            return back()->with('error', 'Endereço não encontrado.');
        }

        $cpf = preg_replace('/\D/', '', $address->cpf ?? '');

        if (strlen($cpf) !== 11) {
            return back()->with('error', 'CPF inválido para boleto.');
        }

        $client = new PaymentClient();

        $expiresAt = now()->addWeekdays(3);

        $payment = $client->create([
            "transaction_amount" => (float) $order->total,
            "description" => "Pedido #" . $order->id,
            "payment_method_id" => "bolbradesco",
            "notification_url" => route('api.webhooks.mercado-pago'),
            "external_reference" => (string) $order->id,
            "date_of_expiration" => $expiresAt->format('Y-m-d\TH:i:s.vP'),
            "payer" => [
                "email" => $order->user->email,
                "first_name" => $order->user->name,
                "identification" => [
                    "type" => "CPF",
                    "number" => $cpf
                ]
            ]
        ]);

        $order->update([
            'gateway_payment_id' => $payment->id,
            'status' => 'pending',
            'gateway_status' => 'pending',
            'expires_at' => $expiresAt,
            'boleto_url' => $payment->transaction_details->external_resource_url
        ]);

        return view('public.payments.methods.boleto', [
            'order' => $order,
            'boleto_url' => $payment->transaction_details->external_resource_url,
            'expires_at' => $expiresAt
        ]);
    }

    /*
    |-------------------------
    | CARTÃO VIEW
    |-------------------------
    */
    public function cardView(int $orderId)
    {
        $order = Order::with('user')->findOrFail($orderId);

        return view('public.payments.methods.card', compact('order'));
    }

    /*
    |-------------------------
    | CARTÃO PROCESSAR
    |-------------------------
    */
    public function processCard(int $orderId, array $data)
    {
        $order = Order::with('user')->findOrFail($orderId);

        if ($order->status === 'paid') {
            return ['success' => true, 'status' => 'paid'];
        }

        DB::beginTransaction();

        try {

            $cpf = preg_replace('/\D/', '', $data['cpf']);

            if (strlen($cpf) !== 11) {
                return ['success' => false, 'error' => 'CPF inválido'];
            }

            $client = new PaymentClient();

            $payment = $client->create([
                "transaction_amount" => (float) $order->total,
                "token" => $data['token'],
                "installments" => (int) $data['installments'],
                "payment_method_id" => $data['payment_method_id'],
                "issuer_id" => (int) $data['issuer_id'],
                "notification_url" => route('api.webhooks.mercado-pago'),
                "external_reference" => (string) $order->id,
                "payer" => [
                    "email" => $order->user->email,
                    "identification" => [
                        "type" => "CPF",
                        "number" => $cpf
                    ]
                ]
            ]);

            $status = match($payment->status) {
                'approved' => 'paid',
                'rejected' => 'failed',
                default => 'pending'
            };

            $order->update([
                'gateway_payment_id' => $payment->id,
                'status' => $status,
                'gateway_status' => $payment->status
            ]);

            if ($status === 'paid') {
                Cart::where('user_id', $order->user_id)
                    ->update(['status' => 'converted']);
            }

            DB::commit();

            return ['success' => true, 'status' => $status];

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Payment error', [
                'message' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => 'Erro interno'];
        }
    }

    /*
    |-------------------------
    | STATUS
    |-------------------------
    */
    public function status(int $orderId): array
    {
        $order = Order::findOrFail($orderId);

        $this->checkExpiration($order);

        return ['status' => $order->status];
    }

    /*
    |-------------------------
    | RESULTADO - SUCESSO
    |-------------------------
    */
    public function success(int $orderId)
    {
        $order = Order::with('user')->findOrFail($orderId);

        return view('public.payments.result.success', [
            'order' => $order
        ]);
    }

    /*
    |-------------------------
    | RESULTADO - ERRO
    |-------------------------
    */
    public function error(int $orderId, ?string $reason = null)
    {
        $order = Order::with('user')->findOrFail($orderId);

        return view('public.payments.result.error', [
            'order'  => $order,
            'reason' => $reason
        ]);
    }

    /*
    |-------------------------
    | HELPERS
    |-------------------------
    */
    private function checkExpiration($order): void
    {
        if ($order->expires_at && now()->greaterThan($order->expires_at)) {
            $order->update([
                'status' => 'expired',
                'gateway_status' => 'expired'
            ]);
        }
    }
}
