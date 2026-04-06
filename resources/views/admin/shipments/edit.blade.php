@extends('layouts.admin')

@section('title', 'Editar Envio')

@section('content')

<h1 class="text-2xl font-bold mb-6">
    Editar Envio
</h1>

<form action="{{ route('admin.shipments.update', $shipment) }}"
      method="POST"
      class="bg-white p-6 rounded shadow space-y-4">

    @csrf
    @method('PUT')

    {{-- 🔒 Transportadora (readonly) --}}
    <div>
        <label class="block font-medium mb-1">
            Transportadora
        </label>

        <input type="text"
               value="{{ $shipment->carrier }}"
               class="w-full border rounded p-2 bg-gray-100"
               readonly>
    </div>

    {{-- 🔒 Custo (readonly) --}}
    <div>
        <label class="block font-medium mb-1">
            Custo do Frete
        </label>

        <input type="text"
               value="R$ {{ number_format($shipment->shipping_cost, 2, ',', '.') }}"
               class="w-full border rounded p-2 bg-gray-100"
               readonly>
    </div>

    {{-- ✏️ Código de rastreio --}}
    <div>
        <label class="block font-medium mb-1">
            Código de Rastreamento
        </label>

        <input type="text"
               name="tracking_code"
               value="{{ old('tracking_code', $shipment->tracking_code) }}"
               class="w-full border rounded p-2"
               placeholder="Ex: BR123456789">
    </div>

    {{-- 🔄 Status --}}
    <div>
        <label class="block font-medium mb-1">
            Status
        </label>

        <select name="status"
                class="w-full border rounded p-2">

            <option value="pending" @selected($shipment->status == 'pending')>
                Pendente
            </option>

            <option value="processing" @selected($shipment->status == 'processing')>
                Preparando
            </option>

            <option value="shipped" @selected($shipment->status == 'shipped')>
                Enviado
            </option>

            <option value="delivered" @selected($shipment->status == 'delivered')>
                Entregue
            </option>

            <option value="cancelled" @selected($shipment->status == 'cancelled')>
                Cancelado
            </option>

        </select>
    </div>

    {{-- Botões --}}
    <div class="flex justify-between items-center pt-4">

        <a href="{{ route('admin.shipments.index') }}"
           class="text-gray-600 hover:text-gray-900">
            Cancelar
        </a>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Atualizar Envio
        </button>

    </div>

</form>

@endsection
