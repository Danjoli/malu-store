<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use App\Services\MelhorEnvioService;

class ShipmentController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | LISTAR ENVIOS
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $shipments = Shipment::with('order.user')
            ->latest()
            ->get();

        return view('admin.shipments.index', compact('shipments'));
    }

    /*
    |----------------------------------------------------------------------
    | EDITAR
    |----------------------------------------------------------------------
    */
    public function edit(Shipment $shipment)
    {
        return view('admin.shipments.edit', compact('shipment'));
    }

    /*
    |----------------------------------------------------------------------
    | ATUALIZAR
    |----------------------------------------------------------------------
    */
    public function update(Request $request, Shipment $shipment)
    {
        if (in_array($shipment->status, ['delivered', 'cancelled'])) {

            return back()->with(
                'error',
                'Este envio não pode mais ser alterado.'
            );
        }

        $request->validate([
            'tracking_code' => 'nullable|string|max:255',
            'status' => 'required|string'
        ]);

        $shipment->update([
            'tracking_code' => $request->tracking_code,
            'status' => $request->status
        ]);

        return redirect()
            ->route('admin.shipments.index')
            ->with('success', 'Envio atualizado!');
    }

    /*
    |----------------------------------------------------------------------
    | GERAR ETIQUETA
    |----------------------------------------------------------------------
    */
    public function gerarEtiqueta($id, MelhorEnvioService $service)
    {
        $shipment = Shipment::with([
            'order.items',
            'order.address',
            'order.user'
        ])->findOrFail($id);

        $order = $shipment->order;

        /*
        |----------------------------------------------------------------------
        | VALIDAÇÕES
        |----------------------------------------------------------------------
        */
        if ($shipment->shipment_id) {

            return back()->with(
                'error',
                'Etiqueta já foi gerada!'
            );
        }

        if ($order->status !== 'paid') {

            return back()->with(
                'error',
                'Pedido ainda não foi pago.'
            );
        }

        if (!$shipment->service_id) {

            return back()->with(
                'error',
                'Serviço de frete não encontrado.'
            );
        }

        if (!$order->address) {

            return back()->with(
                'error',
                'Endereço não encontrado.'
            );
        }

        if (!$order->address->cpf) {

            return back()->with(
                'error',
                'CPF do destinatário não informado.'
            );
        }

        try {

            /*
            |----------------------------------------------------------------------
            | DADOS DA ETIQUETA
            |----------------------------------------------------------------------
            */
            $data = [

                "service" => (int) $shipment->service_id,

                "from" => [
                    "name" => "Sua Loja",
                    "phone" => "11999999999",
                    "email" => "contato@sualoja.com",
                    "document" => "02899542400",
                    "address" => "Rua Origem",
                    "number" => "100",
                    "district" => "Centro",
                    "city" => "São Paulo",
                    "state_abbr" => "SP",
                    "postal_code" => "01010000"
                ],

                "to" => [
                    "name" => $order->address->recipient_name,
                    "phone" => $order->address->phone,
                    "email" => $order->user->email,
                    "document" => preg_replace(
                        '/\D/',
                        '',
                        $order->address->cpf
                    ),
                    "address" => $order->address->street,
                    "number" => $order->address->number,
                    "district" => $order->address->neighborhood,
                    "city" => $order->address->city,
                    "state_abbr" => strtoupper($order->address->state),
                    "postal_code" => preg_replace(
                        '/\D/',
                        '',
                        $order->address->cep
                    )
                ],

                "products" => $order->items->map(function ($item) {

                    return [
                        "name" => $item->name_snapshot,
                        "quantity" => (int) $item->quantity,
                        "unitary_value" => (float) $item->price
                    ];

                })->toArray(),

                "volumes" => [
                    [
                        "weight" => 0.3,
                        "width" => 20,
                        "height" => 5,
                        "length" => 25
                    ]
                ]
            ];

            /*
            |----------------------------------------------------------------------
            | 1. ADICIONAR AO CARRINHO
            |----------------------------------------------------------------------
            */
            $cart = $service->adicionarAoCarrinho($data);

            \Log::info('Carrinho Melhor Envio', [
                'response' => $cart
            ]);

            if (!isset($cart['id'])) {

                \Log::error('Erro carrinho Melhor Envio', [
                    'response' => $cart
                ]);

                return back()->with(
                    'error',
                    $cart['message']
                        ?? 'Erro ao adicionar ao carrinho.'
                );
            }

            /*
            |----------------------------------------------------------------------
            | 2. COMPRAR ETIQUETA
            |----------------------------------------------------------------------
            */
            $purchase = $service->comprarEtiqueta([
                "orders" => [$cart['id']]
            ]);

            \Log::info('Compra etiqueta', [
                'response' => $purchase
            ]);

            /*
            |----------------------------------------------------------------------
            | 3. GERAR ETIQUETA
            |----------------------------------------------------------------------
            */
            $generate = $service->gerarEtiqueta([
                "orders" => [$cart['id']]
            ]);

            \Log::info('Gerar etiqueta', [
                'response' => $generate
            ]);

            /*
            |----------------------------------------------------------------------
            | 4. AGUARDAR PROCESSAMENTO
            |----------------------------------------------------------------------
            */
            sleep(3);

            /*
            |----------------------------------------------------------------------
            | 5. CONSULTAR TRACKING
            |----------------------------------------------------------------------
            */
            $trackingResponse = $service->consultarPedido(
                $cart['id']
            );

            \Log::info('Consulta tracking', [
                'response' => $trackingResponse
            ]);

            /*
            |----------------------------------------------------------------------
            | 6. EXTRAIR DADOS
            |----------------------------------------------------------------------
            */
            $trackingData = current($trackingResponse);

            $trackingCode =
                $trackingData['tracking'] ?? null;

            /*
            |----------------------------------------------------------------------
            | LABEL URL
            |----------------------------------------------------------------------
            */
            $labelUrl =
                "https://sandbox.melhorenvio.com.br/painel/etiquetas/"
                . $cart['id'];

            /*
            |----------------------------------------------------------------------
            | ATUALIZAR BANCO
            |----------------------------------------------------------------------
            */
            $shipment->update([

                'shipment_id' => $cart['id'],

                'tracking_code' => $trackingCode,

                'label_url' => $labelUrl,

                'status' => 'shipped',

                'shipped_at' => now(),

                'last_update' => json_encode($trackingData)
            ]);

            return back()->with(
                'success',
                'Etiqueta gerada com sucesso!'
            );

        } catch (\Exception $e) {

            \Log::error('Erro geral envio', [

                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()

            ]);

            return back()->with(
                'error',
                'Erro ao gerar etiqueta: '
                . $e->getMessage()
            );
        }
    }

    /*
    |----------------------------------------------------------------------
    | ATUALIZAR STATUS MANUAL
    |----------------------------------------------------------------------
    */
    public function atualizarStatus($id, MelhorEnvioService $service)
    {
        $shipment = Shipment::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDAR SHIPMENT ID
        |--------------------------------------------------------------------------
        */
        if (!$shipment->shipment_id) {

            return back()->with(
                'error',
                'Envio não existe na Melhor Envio.'
            );
        }

        try {

            /*
            |--------------------------------------------------------------------------
            | CONSULTAR TRACKING
            |--------------------------------------------------------------------------
            */
            $response = $service->consultarPedido(
                $shipment->shipment_id
            );

            \Log::info('Resposta API Melhor Envio', [
                'data' => $response
            ]);

            /*
            |--------------------------------------------------------------------------
            | VALIDAR RESPOSTA
            |--------------------------------------------------------------------------
            */
            if (
                !$response ||
                isset($response['message'])
            ) {

                return back()->with(
                    'error',
                    'Resposta inválida da API.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | PEGAR PRIMEIRO ITEM DO ARRAY
            |--------------------------------------------------------------------------
            */
            $trackingData = current($response);

            if (!$trackingData) {

                return back()->with(
                    'error',
                    'Tracking não encontrado.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | STATUS API
            |--------------------------------------------------------------------------
            */
            $apiStatus =
                $trackingData['status'] ?? null;

            $status =
                $this->mapStatus($apiStatus)
                ?? $shipment->status;

            /*
            |--------------------------------------------------------------------------
            | TRACKING
            |--------------------------------------------------------------------------
            */
            $trackingCode = !empty($trackingData['tracking'])
                ? $trackingData['tracking']
                : $shipment->tracking_code;

            /*
            |--------------------------------------------------------------------------
            | LABEL URL
            |--------------------------------------------------------------------------
            */
            $labelUrl = $shipment->label_url;

            if (!empty($trackingData['generated_at'])) {

                $labelUrl =
                    "https://sandbox.melhorenvio.com.br/painel/etiquetas/"
                    . $shipment->shipment_id;
            }

            /*
            |--------------------------------------------------------------------------
            | LOG DEBUG
            |--------------------------------------------------------------------------
            */
            \Log::info('Dados processados envio', [

                'shipment_id' => $shipment->shipment_id,
                'api_status' => $apiStatus,
                'mapped_status' => $status,
                'tracking_code' => $trackingCode,
                'label_url' => $labelUrl

            ]);

            /*
            |--------------------------------------------------------------------------
            | ATUALIZAR BANCO
            |--------------------------------------------------------------------------
            */
            $shipment->update([

                'status' => $status,

                'tracking_code' => $trackingCode,

                'label_url' => !empty($labelUrl)
                    ? $labelUrl
                    : $shipment->label_url,

                'shipped_at' =>
                    $apiStatus === 'posted'
                        ? now()
                        : $shipment->shipped_at,

                'delivered_at' =>
                    $apiStatus === 'delivered'
                        ? now()
                        : $shipment->delivered_at,

                'last_update' => json_encode($trackingData)

            ]);

            /*
            |--------------------------------------------------------------------------
            | SUCESSO
            |--------------------------------------------------------------------------
            */
            return back()->with(
                'success',
                'Status atualizado com sucesso!'
            );

        } catch (\Exception $e) {

            \Log::error('Erro ao atualizar status', [

                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()

            ]);

            return back()->with(
                'error',
                'Erro ao atualizar status: ' . $e->getMessage()
            );
        }
    }

    /*
    |----------------------------------------------------------------------
    | WEBHOOK
    |----------------------------------------------------------------------
    */
    public function webhook(Request $request)
    {
        \Log::info('Webhook recebido', [
            'payload' => $request->all()
        ]);

        $data = $request->input('data');

        if (!$data || !isset($data['id'])) {

            return response()->json([
                'ok' => true
            ]);
        }

        $shipment = Shipment::where(
            'shipment_id',
            $data['id']
        )->first();

        if (!$shipment) {

            return response()->json([
                'ok' => true
            ]);
        }

        $apiStatus = $data['status'] ?? null;

        $status = $this->mapStatus($apiStatus)
            ?? $shipment->status;

        $shipment->update([

            'status' => $status,

            'tracking_code' =>
                $data['tracking']
                ?? $shipment->tracking_code,

            'label_url' =>
                $data['label']
                ?? $shipment->label_url,

            'shipped_at' =>
                $apiStatus === 'posted'
                    ? now()
                    : $shipment->shipped_at,

            'delivered_at' =>
                $apiStatus === 'delivered'
                    ? now()
                    : $shipment->delivered_at,

            'last_update' => json_encode($data)
        ]);

        \Log::info('Atualizado via webhook', [

            'shipment_id' => $shipment->id,
            'status' => $status

        ]);

        return response()->json([
            'success' => true
        ]);
    }

    /*
    |----------------------------------------------------------------------
    | MAPEAR STATUS
    |----------------------------------------------------------------------
    */
    private function mapStatus($apiStatus)
    {
        return [

            'created' => 'pending',
            'released' => 'paid',
            'generated' => 'shipped',
            'posted' => 'shipped',
            'in_transit' => 'shipped',
            'delivered' => 'delivered',
            'undelivered' => 'failed',
            'suspended' => 'problem',
            'paused' => 'waiting_action',
            'cancelled' => 'cancelled',

        ][$apiStatus] ?? null;
    }
}
