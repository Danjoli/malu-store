<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Order;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    // LISTAR ENVIOS
    public function index()
    {
        $shipments = Shipment::with('order.user')
            ->latest()
            ->get();

        return view('admin.shipments.index', compact('shipments'));
    }

    // FORM CRIAR
    public function create()
    {
        $orders = Order::with('user')->get();

        return view('admin.shipments.create', compact('orders'));
    }

    // SALVAR ENVIO
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'carrier' => 'required|string|max:255',
            'tracking_code' => 'nullable|string|max:255',
            'shipping_cost' => 'required|numeric',
            'status' => 'required|string'
        ]);

        Shipment::create([
            'order_id' => $request->order_id,
            'carrier' => $request->carrier,
            'tracking_code' => $request->tracking_code,
            'shipping_cost' => $request->shipping_cost,
            'status' => $request->status
        ]);

        return redirect()->route('admin.shipments.index')
            ->with('success', 'Envio criado com sucesso!');
    }

    // FORM EDITAR
    public function edit(Shipment $shipment)
    {
        return view('admin.shipments.edit', compact('shipment'));
    }

    // ATUALIZAR ENVIO
    public function update(Request $request, Shipment $shipment)
    {
        $request->validate([
            'carrier' => 'required|string|max:255',
            'tracking_code' => 'nullable|string|max:255',
            'shipping_cost' => 'required|numeric',
            'status' => 'required|string'
        ]);

        $shipment->update([
            'carrier' => $request->carrier,
            'tracking_code' => $request->tracking_code,
            'shipping_cost' => $request->shipping_cost,
            'status' => $request->status
        ]);

        return redirect()->route('admin.shipments.index')
            ->with('success', 'Envio atualizado!');
    }
}
