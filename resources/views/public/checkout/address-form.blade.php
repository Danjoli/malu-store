<!-- FORMULÁRIO -->
<div class="rounded-md border border-[#eee6e4] bg-white p-6 md:p-7">

    <h2 class="store-title mb-6 text-2xl font-semibold">
        Informações de Entrega
    </h2>

    <!--
    |--------------------------------------------------------------------------
    | ENDEREÇO EXISTENTE
    |--------------------------------------------------------------------------
    -->

    <input type="hidden" name="address_id" value="{{ old('address_id', $address->id ?? '') }}">

    <!-- CEP -->
    <div class="mb-4">
        <label for="cep" class="mb-1 block text-xs font-medium text-stone-700">
            CEP
        </label>

        <input type="text" id="cep" name="cep" value="{{ old('cep', $address->cep ?? '') }}"
            class="store-input" required>
    </div>

    <!-- BOTÃO CALCULAR FRETE -->
    <div class="mb-4">
        <button type="button" id="btn-calcular-frete" class="store-button store-button-primary py-2">
            Calcular Frete
        </button>
    </div>

    <!-- FRETES -->
    <div id="fretes-container" class="mb-6 hidden">
        <label class="block mb-2 font-semibold">
            Escolha o Frete
        </label>

        <div id="lista-fretes" class="space-y-2"></div>
    </div>

    <!-- DADOS DO FRETE -->
    <input type="hidden" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost', 0) }}">

    <input type="hidden" name="carrier" id="carrier" value="{{ old('carrier') }}">

    <input type="hidden" name="service" id="service" value="{{ old('service') }}">

    <!-- NOME DO ENDEREÇO -->
    <div class="mb-4">
        <label for="label" class="block mb-1">
            Nome do Endereço
        </label>

        <input type="text" id="label" name="label" value="{{ old('label', $address->label ?? '') }}"
            class="store-input" placeholder="Ex: Casa, Trabalho (opcional)">
    </div>

    <!-- DESTINATÁRIO -->
    <div class="mb-4">
        <label for="recipient_name" class="block mb-1">
            Nome do Destinatário
        </label>

        <input type="text" id="recipient_name" name="recipient_name"
            value="{{ old('recipient_name', $address->recipient_name ?? '') }}" class="store-input" required>
    </div>

    <!-- TELEFONE -->
    <div class="mb-4">
        <label for="phone" class="block mb-1">
            Telefone
        </label>

        <input type="text" id="phone" name="phone" value="{{ old('phone', $address->phone ?? '') }}"
            class="store-input" required>
    </div>

    <!-- CPF -->
    <div class="mb-4">
        <label for="cpf" class="block mb-1">
            CPF
        </label>

        <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" class="store-input" required>
    </div>

    <!-- RUA -->
    <div class="mb-4">
        <label for="street" class="block mb-1">
            Rua
        </label>

        <input type="text" id="street" name="street" value="{{ old('street', $address->street ?? '') }}"
            class="store-input" required>
    </div>

    <!-- NÚMERO -->
    <div class="mb-4">
        <label for="number" class="block mb-1">
            Número
        </label>

        <input type="text" id="number" name="number" value="{{ old('number', $address->number ?? '') }}"
            class="store-input" required>
    </div>

    <!-- COMPLEMENTO -->
    <div class="mb-4">
        <label for="complement" class="block mb-1">
            Complemento
        </label>

        <input type="text" id="complement" name="complement"
            value="{{ old('complement', $address->complement ?? '') }}" class="store-input">
    </div>

    <!-- BAIRRO -->
    <div class="mb-4">
        <label for="neighborhood" class="block mb-1">
            Bairro
        </label>

        <input type="text" id="neighborhood" name="neighborhood"
            value="{{ old('neighborhood', $address->neighborhood ?? '') }}" class="store-input" required>
    </div>

    <!-- CIDADE -->
    <div class="mb-4">
        <label for="city" class="block mb-1">
            Cidade
        </label>

        <input type="text" id="city" name="city" value="{{ old('city', $address->city ?? '') }}"
            class="store-input" required>
    </div>

    <!-- ESTADO -->
    <div class="mb-4">
        <label for="state" class="block mb-1">
            Estado
        </label>

        <input type="text" id="state" name="state" maxlength="2"
            value="{{ old('state', $address->state ?? '') }}" class="store-input uppercase" required>
    </div>

</div>
