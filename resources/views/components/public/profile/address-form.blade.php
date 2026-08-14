<h3 class="store-title mb-4 mt-8 text-xl font-semibold">
    Adicionar novo endereço
</h3>

<form
    method="POST"
    action="{{ route('addresses.store') }}"
>
    @csrf

    <div class="grid grid-cols-2 gap-3">
        <input
            name="label"
            placeholder="Casa / Trabalho"
            class="store-input"
        >

        <input
            name="recipient_name"
            placeholder="Nome do destinatário"
            class="store-input"
        >

        <input
            name="phone"
            placeholder="Telefone"
            class="store-input"
        >

        <input
            name="cep"
            placeholder="CEP"
            class="store-input"
        >

        <input
            name="street"
            placeholder="Rua"
            class="store-input"
        >

        <input
            name="number"
            placeholder="Número"
            class="store-input"
        >

        <input
            name="neighborhood"
            placeholder="Bairro"
            class="store-input"
        >

        <input
            name="city"
            placeholder="Cidade"
            class="store-input"
        >

        <input
            name="state"
            placeholder="Estado"
            class="store-input"
        >

        <input
            name="complement"
            placeholder="Complemento"
            class="store-input"
        >
    </div>

    <button
        type="submit"
        class="store-button store-button-primary mt-4"
    >
        Salvar Endereço
    </button>
</form>
