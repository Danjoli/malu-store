@extends('layouts.app')

@section('title', 'Checkout')

@section('content')

<div class="max-w-6xl mx-auto px-6 py-10">

    <h1 class="text-3xl font-bold mb-8 tracking-tight">
        Finalizar Compra
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        <!-- FORMULÁRIO DE ENDEREÇO -->
        <div class="bg-white p-6 rounded-xl shadow">

            <h2 class="text-xl font-semibold mb-4">
                Informações de Entrega
            </h2>

            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf

                <input type="hidden" name="address_id" value="{{ $address->id ?? '' }}">

                <div class="mb-4">
                    <label class="block mb-1">CEP</label>
                    <input type="text" name="cep"
                        value="{{ old('cep', $address->cep ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <!-- BOTÃO CALCULAR FRETE -->
                <div class="mb-4">
                    <button type="button" id="btn-calcular-frete"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Calcular Frete
                    </button>
                </div>

                <!-- LISTA DE FRETES -->
                <div id="fretes-container" class="mb-4 hidden">
                    <label class="block mb-2 font-semibold">Escolha o Frete</label>
                    <div id="lista-fretes" class="space-y-2"></div>
                </div>

                <!-- INPUTS HIDDEN -->
                <input type="hidden" name="shipping_cost" id="shipping_cost">
                <input type="hidden" name="carrier" id="carrier">
                <input type="hidden" name="service" id="service"> <!-- 🔥 importante -->

                <!-- RESTO DOS CAMPOS -->
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
                        placeholder="000.000.000-00"
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
                    <label class="block mb-1">Cidade</label>
                    <input type="text" name="city"
                        value="{{ old('city', $address->city ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

        </div>

        <!-- RESUMO DO PEDIDO -->
        <div class="bg-white p-6 rounded-xl shadow">

            <h2 class="text-xl font-semibold mb-4">
                Resumo do Pedido
            </h2>

            @foreach ($cart->items as $item)
                <div class="flex justify-between items-start mb-4">
                    <div class="flex gap-3">
                        <img src="{{ asset('products/' . $item->image_snapshot) }}"
                            class="w-16 h-16 object-cover rounded-lg border">
                        <div>
                            <p class="font-medium">{{ $item->name_snapshot }}</p>
                            <p class="text-sm text-gray-500">
                                Qtd: {{ $item->quantity }}
                            </p>
                        </div>
                    </div>
                    <span class="font-medium">
                        R$ {{ number_format($item->total, 2, ',', '.') }}
                    </span>
                </div>
            @endforeach

            <!-- 🔥 FRETE DINÂMICO -->
            <div class="flex justify-between mb-2 mt-4">
                <span>Frete</span>
                <span id="valor-frete">R$ 0,00</span>
            </div>

            <hr class="my-4">

            <!-- 🔥 TOTAL DINÂMICO -->
            <div class="flex justify-between font-bold text-lg mb-6">
                <span>Total</span>
                <span id="valor-total">
                    R$ {{ number_format($total, 2, ',', '.') }}
                </span>
            </div>

            <button type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700">
                Finalizar Pedido
            </button>

            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>

// CPF máscara
const cpfInput = document.getElementById('cpf');
cpfInput.addEventListener('input', function() {
    let v = this.value.replace(/\D/g,'');
    v = v.replace(/(\d{3})(\d)/,'$1.$2');
    v = v.replace(/(\d{3})(\d)/,'$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/,'$1-$2');
    this.value = v;
});

// TOTAL BASE
const totalBase = {{ $subtotal }};

// CALCULAR FRETE
document.getElementById('btn-calcular-frete').addEventListener('click', async () => {

    const cep = document.querySelector('input[name="cep"]').value;

    const response = await fetch('/frete/calcular', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ cep })
    });

    const data = await response.json();

    const container = document.getElementById('fretes-container');
    const lista = document.getElementById('lista-fretes');

    lista.innerHTML = '';

    data.forEach(frete => {

        if (!frete.price) return;

        const div = document.createElement('div');
        div.classList.add('border', 'p-3', 'rounded-lg');

        div.innerHTML = `
            <label class="flex justify-between items-center">
                <div>
                    <input type="radio" name="frete_opcao"
                        value="${frete.price}"
                        data-carrier="${frete.name}"
                        data-service="${frete.id}">
                    <strong>${frete.name}</strong><br>
                    <small>${frete.delivery_time} dias</small>
                </div>
                <span>R$ ${parseFloat(frete.price).toFixed(2)}</span>
            </label>
        `;

        lista.appendChild(div);
    });

    container.classList.remove('hidden');
});

// ESCOLHER FRETE
document.addEventListener('change', function(e) {
    if (e.target.name === 'frete_opcao') {

        const valor = parseFloat(e.target.value);
        const carrier = e.target.dataset.carrier;
        const service = e.target.dataset.service;

        document.getElementById('shipping_cost').value = valor;
        document.getElementById('carrier').value = carrier;
        document.getElementById('service').value = service;

        // Atualiza frete
        document.getElementById('valor-frete').innerText =
            'R$ ' + valor.toFixed(2);

        // Atualiza total
        const total = totalBase + valor;

        document.getElementById('valor-total').innerText =
            'R$ ' + total.toFixed(2);
    }
});

</script>
@endpush
