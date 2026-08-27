<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shipments\UpdateShipmentRequest;
use App\Jobs\GenerateShipmentLabel;
use App\Models\Shipment;
use App\Services\Admin\Shipments\ShipmentService;

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

        return view('admin.shipments.index.index', compact('shipments'));
    }

    public function edit(Shipment $shipment)
    {
        return view('admin.shipments.edit', compact('shipment'));
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        $this->shipmentService->updateShipment($shipment, $request->validated());

        return redirect()
            ->route('admin.shipments.index')
            ->with('success', 'Envio atualizado!');
    }

    public function gerarEtiqueta($id)
    {
        GenerateShipmentLabel::dispatch((int) $id);

        return back()->with('success', 'A geração da etiqueta foi enviada para processamento.');
    }

    public function atualizarStatus($id)
    {
        $this->shipmentService->syncStatus($id);

        return back()->with('success', 'Status atualizado com sucesso!');
    }
}
