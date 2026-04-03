@extends('layouts.admin')

@section('title', 'Novo Envio')

@section('content')

<h1 class="text-2xl font-bold mb-6">
    Novo Envio
</h1>

<form action="{{ route('admin.shipments.store') }}"
      method="POST"
      class="bg-white p-6 rounded shadow space-y-4">

    @csrf

    {{-- Pedido --}}
    <div>
        <label class="block font-medium mb-1">
            Pedido
        </label>

        <select name="order_id"
                class="w-full border rounded p-2">

            @foreach($orders as $order)
                <option value="{{ $order->id }}">
                    Pedido #{{ $order->id }} - {{ $order->user->name }}
                </option>
            @endforeach

        </select>
    </div>


    {{-- Transportadora --}}
    <div>
        <label class="block font-medium mb-1">
            Transportadora
        </label>

        <input type="text"
               name="carrier"
               class="w-full border rounded p-2"
               placeholder="Ex: Correios, Jadlog, DHL">
    </div>


    {{-- Código de rastreio --}}
    <div>
        <label class="block font-medium mb-1">
            Código de Rastreamento
        </label>

        <input type="text"
               name="tracking_code"
               class="w-full border rounded p-2"
               placeholder="Ex: BR123456789">
    </div>


    {{-- Custo do frete --}}
    <div>
        <label class="block font-medium mb-1">
            Custo do Frete
        </label>

        <input type="number"
               step="0.01"
               name="shipping_cost"
               class="w-full border rounded p-2"
               placeholder="0.00">
    </div>


    {{-- Status --}}
    <div>
        <label class="block font-medium mb-1">
            Status
        </label>

        <select name="status"
                class="w-full border rounded p-2">

            <option value="pending">Pendente</option>
            <option value="processing">Preparando</option>
            <option value="shipped">Enviado</option>
            <option value="delivered">Entregue</option>
            <option value="cancelled">Cancelado</option>

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
            Salvar Envio
        </button>

    </div>

</form>

@endsection
