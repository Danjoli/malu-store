@extends('layouts.public.app')

@section('title', 'Checkout')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

<h1 class="text-3xl font-bold mb-8 tracking-tight">
    Finalizar Compra
</h1>

<form
    id="checkout-form"
    action="{{ route('checkout.process') }}"
    method="POST"
>
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- FORMULÁRIO -->
        <div class="bg-white p-6 rounded-xl shadow">

            <h2 class="text-xl font-semibold mb-6">
                Informações de Entrega
            </h2>

            <!--
            |--------------------------------------------------------------------------
            | ENDEREÇO EXISTENTE
            |--------------------------------------------------------------------------
            -->

            <input
                type="hidden"
                name="address_id"
                value="{{ old('address_id', $address->id ?? '') }}"
            >

            <!-- CEP -->
            <div class="mb-4">
                <label
                    for="cep"
                    class="block mb-1"
                >
                    CEP
                </label>

                <input
                    type="text"
                    id="cep"
                    name="cep"
                    value="{{ old('cep', $address->cep ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2"
                    required
                >
            </div>

            <!-- BOTÃO CALCULAR FRETE -->
            <div class="mb-4">
                <button
                    type="button"
                    id="btn-calcular-frete"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
                >
                    Calcular Frete
                </button>
            </div>

            <!-- FRETES -->
            <div
                id="fretes-container"
                class="mb-6 hidden"
            >
                <label class="block mb-2 font-semibold">
                    Escolha o Frete
                </label>

                <div
                    id="lista-fretes"
                    class="space-y-2"
                ></div>
            </div>

            <!-- DADOS DO FRETE -->
            <input
                type="hidden"
                name="shipping_cost"
                id="shipping_cost"
                value="{{ old('shipping_cost', 0) }}"
            >

            <input
                type="hidden"
                name="carrier"
                id="carrier"
                value="{{ old('carrier') }}"
            >

            <input
                type="hidden"
                name="service"
                id="service"
                value="{{ old('service') }}"
            >

            <!-- NOME DO ENDEREÇO -->
            <div class="mb-4">
                <label
                    for="label"
                    class="block mb-1"
                >
                    Nome do Endereço
                </label>

                <input
                    type="text"
                    id="label"
                    name="label"
                    value="{{ old('label', $address->label ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2"
                    placeholder="Ex: Casa, Trabalho (opcional)"
                >
            </div>

            <!-- DESTINATÁRIO -->
            <div class="mb-4">
                <label
                    for="recipient_name"
                    class="block mb-1"
                >
                    Nome do Destinatário
                </label>

                <input
                    type="text"
                    id="recipient_name"
                    name="recipient_name"
                    value="{{ old('recipient_name', $address->recipient_name ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2"
                    required
                >
            </div>

            <!-- TELEFONE -->
            <div class="mb-4">
                <label
                    for="phone"
                    class="block mb-1"
                >
                    Telefone
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="{{ old('phone', $address->phone ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2"
                    required
                >
            </div>

            <!-- CPF -->
            <div class="mb-4">
                <label
                    for="cpf"
                    class="block mb-1"
                >
                    CPF
                </label>

                <input
                    type="text"
                    id="cpf"
                    name="cpf"
                    value="{{ old('cpf') }}"
                    class="w-full border rounded-lg px-4 py-2"
                    required
                >
            </div>

            <!-- RUA -->
            <div class="mb-4">
                <label
                    for="street"
                    class="block mb-1"
                >
                    Rua
                </label>

                <input
                    type="text"
                    id="street"
                    name="street"
                    value="{{ old('street', $address->street ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2"
                    required
                >
            </div>

            <!-- NÚMERO -->
            <div class="mb-4">
                <label
                    for="number"
                    class="block mb-1"
                >
                    Número
                </label>

                <input
                    type="text"
                    id="number"
                    name="number"
                    value="{{ old('number', $address->number ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2"
                    required
                >
            </div>

            <!-- COMPLEMENTO -->
            <div class="mb-4">
                <label
                    for="complement"
                    class="block mb-1"
                >
                    Complemento
                </label>

                <input
                    type="text"
                    id="complement"
                    name="complement"
                    value="{{ old('complement', $address->complement ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2"
                >
            </div>

            <!-- BAIRRO -->
            <div class="mb-4">
                <label
                    for="neighborhood"
                    class="block mb-1"
                >
                    Bairro
                </label>

                <input
                    type="text"
                    id="neighborhood"
                    name="neighborhood"
                    value="{{ old('neighborhood', $address->neighborhood ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2"
                    required
                >
            </div>

            <!-- CIDADE -->
            <div class="mb-4">
                <label
                    for="city"
                    class="block mb-1"
                >
                    Cidade
                </label>

                <input
                    type="text"
                    id="city"
                    name="city"
                    value="{{ old('city', $address->city ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2"
                    required
                >
            </div>

            <!-- ESTADO -->
            <div class="mb-4">
                <label
                    for="state"
                    class="block mb-1"
                >
                    Estado
                </label>

                <input
                    type="text"
                    id="state"
                    name="state"
                    maxlength="2"
                    value="{{ old('state', $address->state ?? '') }}"
                    class="w-full border rounded-lg px-4 py-2 uppercase"
                    required
                >
            </div>

        </div>

        <!-- RESUMO -->
        <div class="bg-white p-6 rounded-xl shadow h-fit">

            <h2 class="text-xl font-semibold mb-6">
                Resumo do Pedido
            </h2>

            <!-- ITENS -->
            <div class="space-y-4">

                @foreach (($cart?->items ?? []) as $item)

                    <div class="flex justify-between gap-4">

                        <span>
                            {{ $item->name_snapshot }}
                            (x{{ $item->quantity }})
                        </span>

                        <span class="whitespace-nowrap">
                            R$
                            {{ number_format(
                                $item->price * $item->quantity,
                                2,
                                ',',
                                '.'
                            ) }}
                        </span>

                    </div>

                @endforeach

            </div>

            <!-- FRETE -->
            <div class="flex justify-between mb-2 mt-6">

                <span>
                    Frete
                </span>

                <span id="valor-frete">
                    R$ 0,00
                </span>

            </div>

            <hr class="my-4">

            <!-- TOTAL -->
            <div class="flex justify-between font-bold text-lg mb-6">

                <span>
                    Total
                </span>

                <span id="valor-total">
                    R$
                    {{ number_format(
                        $total ?? $subtotal ?? 0,
                        2,
                        ',',
                        '.'
                    ) }}
                </span>

            </div>

            <!-- FINALIZAR -->
            <button
                type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition"
            >
                Finalizar Pedido
            </button>

        </div>

    </div>

</form>

</div>

@endsection

@push('scripts')

<script>
    window.SUBTOTAL = @json($subtotal ?? 0);
    window.CSRF_TOKEN = @json(csrf_token());
</script>

@endpush
