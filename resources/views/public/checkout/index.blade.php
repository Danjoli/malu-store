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
                    <label class="block mb-1">Identificação do Endereço</label>
                    <input type="text" name="label" placeholder="Ex: Casa, Trabalho"
                        value="{{ old('label', $address->label ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

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
                    <label class="block mb-1">Complemento</label>
                    <input type="text" name="complement"
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
                    <input type="text" name="state" maxlength="2"
                        placeholder="SP"
                        value="{{ old('state', $address->state ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block mb-1">CEP</label>
                    <input type="text" name="cep"
                        value="{{ old('cep', $address->cep ?? '') }}"
                        class="w-full border rounded-lg px-4 py-2">
                </div>

                <div class="mb-4 flex items-center gap-2">
                    <input type="checkbox" name="is_default" value="1"
                        {{ old('is_default') !== null
                            ? (old('is_default') ? 'checked' : '')
                            : (($address->is_default ?? false) ? 'checked' : '') }}>
                    <label>Definir como endereço padrão</label>
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
                            alt="{{ $item->name_snapshot }}"
                            class="w-16 h-16 object-cover rounded-lg border">
                        <div>
                            <p class="font-medium">{{ $item->name_snapshot }}</p>
                            <p class="text-sm text-gray-500">
                                Qtd: {{ $item->quantity }}
                                @if($item->color_snapshot) • Cor: {{ $item->color_snapshot }} @endif
                                @if($item->size_snapshot) • Tam: {{ $item->size_snapshot }} @endif
                            </p>
                        </div>
                    </div>
                    <span class="font-medium">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                </div>
            @endforeach

            <div class="flex justify-between mb-2 mt-4">
                <span>Frete</span>
                <span>R$ {{ number_format($shipping, 2, ',', '.') }}</span>
            </div>

            <hr class="my-4">

            <div class="flex justify-between font-bold text-lg mb-6">
                <span>Total</span>
                <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
            </div>

            <!-- BOTÃO FINALIZAR PEDIDO -->
            <button type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
                Finalizar Pedido
            </button>

            </form>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    // Máscara simples de CPF
    const cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function(e) {
        let v = this.value.replace(/\D/g,'');
        v = v.replace(/(\d{3})(\d)/,'$1.$2');
        v = v.replace(/(\d{3})(\d)/,'$1.$2');
        v = v.replace(/(\d{3})(\d{1,2})$/,'$1-$2');
        this.value = v;
    });
</script>
@endpush