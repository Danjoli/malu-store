<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

use App\Models\Order;

class PaymentController extends Controller
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.token'));
    }

    /*
    |--------------------------------------------------------------------------
    | PIX
    |--------------------------------------------------------------------------
    */
    public function createPix($orderId)
    {
        $order = Order::with('user')->findOrFail($orderId);

        // bloqueia se expirado
        if ($order->expires_at && now()->greaterThan($order->expires_at)) {
            return redirect()->route('payment', $order->id)
                ->with('error', 'Este pagamento expirou.');
        }

        $client = new PaymentClient();

        $payment = $client->create([
            "transaction_amount" => (float) $order->total,
            "description" => "Pedido #" . $order->id,
            "payment_method_id" => "pix",
            "notification_url" => route('webhook.mercadopago'),
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

        return view('public.payment.pix', [
            'order' => $order,
            'expires_at' => $expiresAt,
            'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
            'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BOLETO
    |--------------------------------------------------------------------------
    */
    public function createBoleto($orderId)
    {
        $order = Order::with(['user', 'address', 'user.addresses'])->findOrFail($orderId);

        // bloqueia se expirado
        if (
            $order->expires_at &&
            now()->greaterThan($order->expires_at) &&
            !in_array($order->status, ['paid', 'cancelled'])
        ) {
            $order->update([
                'status' => 'expired',
                'gateway_status' => 'expired'
            ]);
        
            return redirect()->route('payment.error', [
                'order' => $order->id,
                'reason' => 'expired'
            ]);
        }
        $client = new PaymentClient();

        $address = $order->address ?? $order->user->addresses->first();

        if (!$address) {
            return back()->with('error', 'Endereço não encontrado.');
        }

        $cpf = preg_replace('/\D/', '', $address->cpf ?? '');

        if (strlen($cpf) !== 11) {
            return back()->with('error', 'CPF inválido para boleto.');
        }

        try {

            $expiresAt = now()->addWeekdays(3); // 3 dias úteis
            $dateOfExpiration = $expiresAt->format('Y-m-d\TH:i:s.vP');

            $payment = $client->create([
                "transaction_amount" => (float) $order->total,
                "description" => "Pedido #" . $order->id,
                "payment_method_id" => "bolbradesco",
                "notification_url" => route('webhook.mercadopago'),
                "external_reference" => (string) $order->id,
                "date_of_expiration" => $dateOfExpiration,

                "payer" => [
                    "email" => $order->user->email,
                    "first_name" => $order->user->name ?? 'Cliente',
                    "last_name" => "Cliente",

                    "identification" => [
                        "type" => "CPF",
                        "number" => $cpf
                    ],

                    "address" => [
                        "zip_code" => preg_replace('/\D/', '', $address->cep),
                        "street_name" => $address->street,
                        "street_number" => $address->number,
                        "neighborhood" => $address->neighborhood,
                        "city" => $address->city,
                        "federal_unit" => strtoupper($address->state)
                    ]
                ]
            ]);

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            dd($e->getApiResponse()->getContent());
        }

        $order->update([
            'gateway_payment_id' => $payment->id,
            'status' => 'pending',
            'gateway_status' => 'pending',
            'expires_at' => $expiresAt,
            'boleto_url' => $payment->transaction_details->external_resource_url
        ]);

        return view('public.payment.boleto', [
            'order' => $order,
            'boleto_url' => $payment->transaction_details->external_resource_url,
            'expires_at' => $expiresAt
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CARTÃO
    |--------------------------------------------------------------------------
    */
    
    public function createCard($orderId)
    {
        $order = Order::with('user')->findOrFail($orderId);
    
        return view('public.payment.card', compact('order'));
    }
    
    public function processCard(Request $request, $orderId)
    {
        // 🔒 Validação dos dados
        $request->validate([
            'token' => 'required|string',
            'payment_method_id' => 'required|string',
            'issuer_id' => 'required|integer',
            'installments' => 'required|integer|min:1',
            'cpf' => 'required|string',
        ]);
    
        $order = Order::with('user')->findOrFail($orderId);
    
        // 🔥 Proteção contra pagamento duplicado
        if ($order->status === 'paid') {
            return response()->json([
                'success' => true,
                'status' => 'paid'
            ]);
        }
    
        $client = new PaymentClient();
    
        \DB::beginTransaction();
    
        try {
    
            // 🔄 Recarrega pedido dentro da transação
            $order->refresh();
    
            if ($order->status === 'paid') {
                \DB::rollBack();
                return response()->json(['status' => 'paid']);
            }
    
            $payment = $client->create([
                "transaction_amount" => (float) $order->total,
                "token" => $request->token,
                "description" => "Pedido #" . $order->id,
                "installments" => (int) $request->installments,
                "payment_method_id" => $request->payment_method_id,
                "issuer_id" => (int) $request->issuer_id,
                "notification_url" => route('webhook.mercadopago'),
                "external_reference" => (string) $order->id,
    
                "payer" => [
                    "email" => $request->email ?? $order->user->email,
                    "identification" => [
                        "type" => "CPF",
                        "number" => preg_replace('/\D/', '', $request->cpf)
                    ]
                ]
            ]);
    
            // 🔥 Mapear status
            $status = match($payment->status) {
                'approved' => 'paid',
                'pending', 'in_process' => 'pending',
                'rejected' => 'failed',
                default => 'pending'
            };
    
            // 💾 Atualizar pedido
            $order->update([
                'gateway_payment_id' => $payment->id,
                'status' => $status,
                'gateway_status' => $payment->status
            ]);
            
            // 🔥 TESTE DIRETO (SEM WEBHOOK)
            if ($status === 'paid') {
            
                \Log::info('🔥 TESTE CONVERTENDO CARRINHO DIRETO', [
                    'user_id' => $order->user_id
                ]);
            
                Cart::where('user_id', $order->user_id)
                    ->update([
                        'status' => 'converted'
                    ]);
            }
    
            \DB::commit();
    
            return response()->json([
                'success' => true,
                'status' => $status
            ]);
    
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
    
            \DB::rollBack();
    
            $response = $e->getApiResponse()->getContent();
    
            \Log::error('Erro MercadoPago', [
                'response' => $response
            ]);
    
            return response()->json([
                'success' => false,
                'error' => $response
            ], 500);
    
        } catch (\Exception $e) {
    
            \DB::rollBack();
    
            \Log::error('Erro geral pagamento', [
                'error' => $e->getMessage()
            ]);
    
            return response()->json([
                'success' => false,
                'error' => 'Erro interno'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS DO PAGAMENTO
    |--------------------------------------------------------------------------
    */
    public function status($orderId)
    {
        $order = Order::findOrFail($orderId);

        if (
            $order->expires_at &&
            now()->greaterThan($order->expires_at) &&
            !in_array($order->status, ['paid', 'cancelled'])
        ) {
            $order->update([
                'status' => 'expired',
                'gateway_status' => 'expired'
            ]);

            $order->status = 'expired';
        }

        return response()->json([
            'status' => $order->status
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RESULTADOS
    |--------------------------------------------------------------------------
    */
    public function success($orderId)
    {
        $order = Order::find($orderId);

        if (!$order || $order->status !== 'paid') {
            return redirect('/')->with('error', 'Pagamento não confirmado.');
        }

        return view('public.payment.result.success', compact('order'));
    }

    public function error($orderId, Request $request)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return redirect('/')->with('error', 'Pedido não encontrado.');
        }

        $reason = $request->query('reason', 'failed');

        return view('public.payment.result.error', compact('order', 'reason'));
    }
}