<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shipments\UpdateShipmentRequest;
use App\Jobs\GenerateShipmentLabelJob;
use App\Models\Shipment;
use App\Services\Admin\Shipments\ShipmentService;
use App\UseCases\Shipping\SyncShipmentStatus;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipmentService,
        protected SyncShipmentStatus $syncShipmentStatus,
    ) {}

    public function index(Request $request)
    {
        $shipments = Shipment::with('order.user')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $term = trim($request->string('q')->toString());
                $search = '%'.$term.'%';
                $query->where(function ($query) use ($term, $search): void {
                    if (ctype_digit($term)) {
                        $query->orWhere('order_id', (int) $term);
                    }
                    $query->orWhere('tracking_code', 'like', $search)
                        ->orWhere('carrier', 'like', $search)
                        ->orWhereHas('order.user', fn ($query) => $query->where('name', 'like', $search));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('carrier'), fn ($query) => $query->where('carrier', $request->string('carrier')->toString()))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.shipments.index.index', [
            'shipments' => $shipments,
            'carriers' => Shipment::query()->whereNotNull('carrier')->distinct()->orderBy('carrier')->pluck('carrier'),
        ]);
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
        GenerateShipmentLabelJob::dispatch((int) $id);

        return back()->with('success', 'A geração da etiqueta foi enviada para processamento.');
    }

    public function atualizarStatus($id)
    {
        $this->syncShipmentStatus->execute((int) $id);

        return back()->with('success', 'Status atualizado com sucesso!');
    }
}
