<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\Admins\Shipment\ShipmentService;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService
    ) {}

    public function index()
    {
        $shipments = Shipment::with('order.user')
            ->latest()
            ->get();

        return view('admin.shipments.index', compact('shipments'));
    }

    public function edit(Shipment $shipment)
    {
        return view('admin.shipments.edit', compact('shipment'));
    }

    public function update(Request $request, Shipment $shipment)
    {
        $this->shipmentService->updateShipment($shipment, $request->all());

        return redirect()
            ->route('admin.shipments.index')
            ->with('success', 'Envio atualizado!');
    }

    public function gerarEtiqueta($id)
    {
        $this->shipmentService->generateLabel($id);

        return back()->with('success', 'Etiqueta gerada com sucesso!');
    }

    public function atualizarStatus($id)
    {
        $this->shipmentService->syncStatus($id);

        return back()->with('success', 'Status atualizado com sucesso!');
    }
}
