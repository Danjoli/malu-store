{{-- Formulário --}}
<div class="rounded-md border border-[#eee6e4] bg-white p-6 md:p-7">
    <h2 class="store-title mb-6 text-2xl font-semibold">
        Informações de Entrega
    </h2>

    {{-- Endereço existente --}}
    <input
        type="hidden"
        name="address_id"
        value="{{ old('address_id', $address->id ?? '') }}"
    >

    {{-- CEP --}}
    <div class="mb-4">
        <label for="cep" class="mb-1 block text-xs font-medium text-stone-700">
            CEP
        </label>

        <input
            type="text"
            id="cep"
            name="cep"
            value="{{ old('cep', $address->cep ?? '') }}"
            class="store-input"
            required
        >
    </div>

    {{-- Calcular frete --}}
    <div class="mb-4">
        <button
            type="button"
            id="btn-calcular-frete"
            class="store-button store-button-primary py-2"
        >
            Calcular Frete
        </button>
    </div>

    {{-- Fretes --}}
    <div id="fretes-container" class="mb-6 hidden">
        <label class="mb-2 block font-semibold">
            Escolha o Frete
        </label>

        <div id="lista-fretes" class="space-y-2"></div>
    </div>

    {{-- Dados do frete --}}
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

    {{-- Nome do endereço --}}
    <div class="mb-4">
        <label for="label" class="mb-1 block">
            Nome do Endereço
        </label>

        <input
            type="text"
            id="label"
            name="label"
            value="{{ old('label', $address->label ?? '') }}"
            class="store-input"
            placeholder="Ex: Casa, Trabalho (opcional)"
        >
    </div>

    {{-- Destinatário --}}
    <div class="mb-4">
        <label for="recipient_name" class="mb-1 block">
            Nome do Destinatário
        </label>

        <input
            type="text"
            id="recipient_name"
            name="recipient_name"
            value="{{ old('recipient_name', $address->recipient_name ?? '') }}"
            class="store-input"
            required
        >
    </div>

    {{-- Telefone --}}
    <div class="mb-4">
        <label for="phone" class="mb-1 block">
            Telefone
        </label>

        <input
            type="text"
            id="phone"
            name="phone"
            value="{{ old('phone', $address->phone ?? '') }}"
            class="store-input"
            required
        >
    </div>

    {{-- CPF --}}
    <div class="mb-4">
        <label for="cpf" class="mb-1 block">
            CPF
        </label>

        <input
            type="text"
            id="cpf"
            name="cpf"
            value="{{ old('cpf') }}"
            class="store-input"
            required
        >
    </div>

    {{-- Rua --}}
    <div class="mb-4">
        <label for="street" class="mb-1 block">
            Rua
        </label>

        <input
            type="text"
            id="street"
            name="street"
            value="{{ old('street', $address->street ?? '') }}"
            class="store-input"
            required
        >
    </div>

    {{-- Número --}}
    <div class="mb-4">
        <label for="number" class="mb-1 block">
            Número
        </label>

        <input
            type="text"
            id="number"
            name="number"
            value="{{ old('number', $address->number ?? '') }}"
            class="store-input"
            required
        >
    </div>

    {{-- Complemento --}}
    <div class="mb-4">
        <label for="complement" class="mb-1 block">
            Complemento
        </label>

        <input
            type="text"
            id="complement"
            name="complement"
            value="{{ old('complement', $address->complement ?? '') }}"
            class="store-input"
        >
    </div>

    {{-- Bairro --}}
    <div class="mb-4">
        <label for="neighborhood" class="mb-1 block">
            Bairro
        </label>

        <input
            type="text"
            id="neighborhood"
            name="neighborhood"
            value="{{ old('neighborhood', $address->neighborhood ?? '') }}"
            class="store-input"
            required
        >
    </div>

    {{-- Cidade --}}
    <div class="mb-4">
        <label for="city" class="mb-1 block">
            Cidade
        </label>

        <input
            type="text"
            id="city"
            name="city"
            value="{{ old('city', $address->city ?? '') }}"
            class="store-input"
            required
        >
    </div>

    {{-- Estado --}}
    <div class="mb-4">
        <label for="state" class="mb-1 block">
            Estado
        </label>

        <input
            type="text"
            id="state"
            name="state"
            maxlength="2"
            value="{{ old('state', $address->state ?? '') }}"
            class="store-input uppercase"
            required
        >
    </div>
</div>
