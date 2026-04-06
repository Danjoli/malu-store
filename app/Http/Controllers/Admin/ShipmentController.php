<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\MelhorEnvioService;

class ShipmentController extends Controller
{
    // 📦 LISTAR ENVIOS
    public function index()
    {
        $shipments = Shipment::with('order.user')
            ->latest()
            ->get();

        return view('admin.shipments.index', compact('shipments'));
    }

    // ❌ REMOVEMOS CREATE (não faz sentido manual)
    // Os envios já são criados no checkout

    // ✏️ EDITAR
    public function edit(Shipment $shipment)
    {
        return view('admin.shipments.edit', compact('shipment'));
    }

    // 🔄 ATUALIZAR
    public function update(Request $request, Shipment $shipment)
    {
        $request->validate([
            'carrier' => 'required|string|max:255',
            'tracking_code' => 'nullable|string|max:255',
            'shipping_cost' => 'required|numeric',
            'status' => 'required|string'
        ]);

        $shipment->update($request->only([
            'carrier',
            'tracking_code',
            'shipping_cost',
            'status'
        ]));

        return redirect()->route('admin.shipments.index')
            ->with('success', 'Envio atualizado!');
    }

    // 🚀 GERAR ETIQUETA (🔥 PRINCIPAL)
    public function gerarEtiqueta($id, MelhorEnvioService $service)
    {
        $shipment = Shipment::with('order.items', 'order.address', 'order.user')
            ->findOrFail($id);

        $order = $shipment->order;

        // 🔒 Evita duplicação
        if ($shipment->tracking_code) {
            return back()->with('error', 'Etiqueta já foi gerada!');
        }

        // 🔒 Só após pagamento
        if ($order->status !== 'paid') {
            return back()->with('error', 'Pedido ainda não foi pago.');
        }

        try {

            // 📦 Montar payload
            $data = [
                "service" => $shipment->shipment_id, // 🔥 ESSENCIAL
                "from" => [
                    "name" => "Sua Loja",
                    "phone" => "11999999999",
                    "email" => "contato@sualoja.com",
                    "address" => "Rua Origem",
                    "number" => "100",
                    "city" => "São Paulo",
                    "state_abbr" => "SP",
                    "postal_code" => "01010-000"
                ],
                "to" => [
                    "name" => $order->address->recipient_name,
                    "phone" => $order->address->phone,
                    "email" => $order->user->email,
                    "address" => $order->address->street,
                    "number" => $order->address->number,
                    "district" => $order->address->neighborhood,
                    "city" => $order->address->city,
                    "state_abbr" => $order->address->state,
                    "postal_code" => $order->address->cep
                ],
                "products" => $order->items->map(function ($item) {
                    return [
                        "name" => $item->name_snapshot,
                        "quantity" => $item->quantity,
                        "unitary_value" => $item->price,
                        "weight" => 1,
                        "width" => 15,
                        "height" => 10,
                        "length" => 20
                    ];
                })->toArray()
            ];

            // 🛒 Adicionar ao carrinho
            $cart = $service->adicionarAoCarrinho($data);

            // 💳 Comprar etiqueta
            $checkout = $service->comprarEtiqueta([
                "orders" => [$cart['id']]
            ]);

            // 📌 Atualizar banco
            $shipment->update([
                'tracking_code' => $checkout['tracking'] ?? null,
                'status' => 'shipped',
                'shipped_at' => now()
            ]);

            return back()->with('success', 'Etiqueta gerada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar etiqueta: ' . $e->getMessage());
        }
    }
}
