<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Http\Request;
use App\Services\MelhorEnvioService;
use Illuminate\Support\Facades\Http;

class ShipmentController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | LISTAR ENVIOS
    |----------------------------------------------------------------------
    */
    public function index()
    {
        $shipments = Shipment::with('order.user')->latest()->get();
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
            return back()->with('error', 'Este envio não pode mais ser alterado.');
        }

        $request->validate([
            'tracking_code' => 'nullable|string|max:255',
            'status' => 'required|string'
        ]);

        $shipment->update([
            'tracking_code' => $request->tracking_code,
            'status' => $request->status
        ]);

        return redirect()->route('admin.shipments.index')
            ->with('success', 'Envio atualizado!');
    }

    /*
    |----------------------------------------------------------------------
    | GERAR ETIQUETA
    |----------------------------------------------------------------------
    */
    public function gerarEtiqueta($id, MelhorEnvioService $service)
    {
        $shipment = Shipment::with('order.items', 'order.address', 'order.user')
            ->findOrFail($id);

        $order = $shipment->order;

        if ($shipment->tracking_code) {
            return back()->with('error', 'Etiqueta já foi gerada!');
        }

        if ($order->status !== 'paid') {
            return back()->with('error', 'Pedido ainda não foi pago.');
        }

        if (!$order->address->cpf) {
            return back()->with('error', 'CPF do destinatário não informado.');
        }

        try {
            $data = [
                "service" => $shipment->shipment_id,
                "from" => [
                    "name" => "Sua Loja",
                    "phone" => "11999999999",
                    "email" => "contato@sualoja.com",
                    "document" => "02899542400",
                    "address" => "Rua Origem",
                    "number" => "100",
                    "city" => "São Paulo",
                    "state_abbr" => "SP",
                    "postal_code" => "01010000"
                ],
                "to" => [
                    "name" => $order->address->recipient_name,
                    "phone" => $order->address->phone,
                    "email" => $order->user->email,
                    "document" => preg_replace('/\D/', '', $order->address->cpf),
                    "address" => $order->address->street,
                    "number" => $order->address->number,
                    "district" => $order->address->neighborhood,
                    "city" => $order->address->city,
                    "state_abbr" => $order->address->state,
                    "postal_code" => preg_replace('/\D/', '', $order->address->cep)
                ],
                "products" => $order->items->map(function ($item) {
                    return [
                        "name" => $item->name_snapshot,
                        "quantity" => $item->quantity,
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

            $cart = $service->adicionarAoCarrinho($data);

            if (!isset($cart['id'])) {
                \Log::error('Erro carrinho Melhor Envio', $cart);
                return back()->with('error', 'Erro ao adicionar ao carrinho.');
            }

            $cartId = $cart['id'];

            $purchase = $service->comprarEtiqueta([
                "orders" => [$cartId]
            ]);

            $orderData = $purchase['purchase']['orders'][0] ?? $purchase['orders'][0] ?? null;

            if (!$orderData) {
                \Log::error('Erro ao obter dados do pedido comprado', $purchase);
                return back()->with('error', 'Não foi possível obter dados do pedido.');
            }

            $trackingCode = $orderData['tracking']
                            ?? $orderData['tracking_code']
                            ?? $orderData['protocol']
                            ?? null;

            $labelUrl = $orderData['labels'][0]['url']
                        ?? $orderData['label_url']
                        ?? $orderData['label_pdf']
                        ?? $orderData['service']['company']['tracking_link']
                        ?? null;

            $shipment->update([
                'tracking_code' => $trackingCode,
                'label_url' => $labelUrl,
                'status' => 'shipped',
                'shipped_at' => now()
            ]);

            return back()->with('success', 'Etiqueta gerada com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro geral envio', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Erro ao gerar etiqueta: ' . $e->getMessage());
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

        if (!$shipment->shipment_id) {
            return back()->with('error', 'Envio ainda não foi gerado na Melhor Envio.');
        }

        try {
            $orderData = $service->consultarPedido($shipment->shipment_id);

            \Log::info('Resposta API Melhor Envio', $orderData);

            $apiStatus = $orderData['status'] ?? null;

            // Mapeamento correto
            $statusMap = [
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
            ];

            $status = $statusMap[$apiStatus] ?? $shipment->status;

            $shipment->update([
                'status' => $status,
                'shipped_at' => $apiStatus === 'posted' ? now() : $shipment->shipped_at,
                'delivered_at' => $apiStatus === 'delivered' ? now() : $shipment->delivered_at,
                'tracking_code' => $orderData['tracking'] ?? $shipment->tracking_code
            ]);

            return back()->with('success', 'Status atualizado manualmente!');

        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar status', [
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', 'Não foi possível atualizar o status.');
        }
    }

    /*
    |----------------------------------------------------------------------
    | WEBHOOK
    |----------------------------------------------------------------------
    */
    public function webhook(Request $request)
    {
        \Log::info('Webhook Melhor Envio recebido', $request->all());

        $event = $request->input('event');
        $data = $request->input('data');

        if (!$data) {
            return response()->json(['error' => 'Payload inválido'], 400);
        }

        $shipment = Shipment::where('shipment_id', $data['id'])->first();

        if (!$shipment) {
            return response()->json(['error' => 'Shipment não encontrado'], 404);
        }

        $apiStatus = $data['status'] ?? null;

        $statusMap = [
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
        ];

        $status = $statusMap[$apiStatus] ?? $shipment->status;

        $shipment->update([
            'status' => $status,
            'tracking_code' => $data['tracking'] ?? $shipment->tracking_code,
            'shipped_at' => $apiStatus === 'posted' ? now() : $shipment->shipped_at,
            'delivered_at' => $apiStatus === 'delivered' ? now() : $shipment->delivered_at,
        ]);

        \Log::info("Envio atualizado via webhook", [
            'shipment_id' => $shipment->id,
            'status' => $status
        ]);

        return response()->json(['success' => true]);
    }
}
