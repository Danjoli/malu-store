@extends('layouts.public.app')

@section('title', 'Checkout')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

    <h1 class="text-3xl font-bold mb-8 tracking-tight">
        Finalizar Compra
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- FORMULÁRIO -->
        <div class="bg-white p-6 rounded-xl shadow">

            <h2 class="text-xl font-semibold mb-4">
                Informações de Entrega
            </h2>

            <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
                @csrf

                <input type="hidden" name="address_id" value="{{ $address->id ?? '' }}">

                <!-- CEP -->
                <div class="mb-4">
                    <label class="block mb-1">CEP</label>
                    <input type="text" name="cep"
                        value="{{ old('cep', $address->cep ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <!-- BOTÃO FRETE -->
                <div class="mb-4">
                    <button type="button" id="btn-calcular-frete"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Calcular Frete
                    </button>
                </div>

                <!-- FRETES -->
                <div id="fretes-container" class="mb-4 hidden">
                    <label class="block mb-2 font-semibold">Escolha o Frete</label>
                    <div id="lista-fretes" class="space-y-2"></div>
                </div>

                <!-- HIDDEN -->
                <input type="hidden" name="shipping_cost" id="shipping_cost">
                <input type="hidden" name="carrier" id="carrier">
                <input type="hidden" name="service" id="service">

                <!-- DADOS -->
                <div class="mb-4">
                    <label class="block mb-1">Nome do Destinatário</label>
                    <input type="text" name="recipient_name"
                        value="{{ old('recipient_name', $address->recipient_name ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Telefone</label>
                    <input type="text" name="phone"
                        value="{{ old('phone', $address->phone ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block mb-1">CPF</label>
                    <input type="text" name="cpf" id="cpf"
                        value="{{ old('cpf', $address->cpf ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Rua</label>
                    <input type="text" name="street"
                        value="{{ old('street', $address->street ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Número</label>
                    <input type="text" name="number"
                        value="{{ old('number', $address->number ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Complemento</label>
                    <input type="text"
                        name="complement"
                        value="{{ old('complement', $address->complement ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Bairro</label>
                    <input type="text" name="neighborhood"
                        value="{{ old('neighborhood', $address->neighborhood ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Cidade</label>
                    <input type="text" name="city"
                        value="{{ old('city', $address->city ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block mb-1">Estado</label>
                    <input type="text" name="state"
                        maxlength="2"
                        value="{{ old('state', $address->state ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

            </div>

            <!-- RESUMO -->
            <div class="bg-white p-6 rounded-xl shadow">

                <h2 class="text-xl font-semibold mb-4">
                    Resumo do Pedido
                </h2>

                @foreach (($cart?->items ?? []) as $item)
                    <div class="flex justify-between mb-4">
                        <span>{{ $item->name_snapshot }} (x{{ $item->quantity }})</span>
                        <span>R$ {{ number_format(($item->price * $item->quantity), 2, ',', '.') }}</span>
                    </div>
                @endforeach

                <div class="flex justify-between mb-2 mt-4">
                    <span>Frete</span>
                    <span id="valor-frete">R$ 0,00</span>
                </div>

                <hr class="my-4">

                <div class="flex justify-between font-bold text-lg mb-6">
                    <span>Total</span>
                    <span id="valor-total">
                        R$ {{ number_format($total ?? 0, 2, ',', '.') }}
                    </span>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-lg">
                    Finalizar Pedido
                </button>

            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    window.SUBTOTAL = @json($subtotal);
    window.CSRF_TOKEN = @json(csrf_token());
</script>
@endpush

