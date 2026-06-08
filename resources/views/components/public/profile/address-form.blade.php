<h3 class="font-semibold mt-6 mb-4">
    Adicionar novo endereço
</h3>

<form method="POST" action="{{ route('profile.address.store') }}">

    @csrf

    <div class="grid grid-cols-2 gap-3">

        <input
            name="label"
            placeholder="Casa / Trabalho"
            class="border p-2 rounded"
        >

        <input
            name="recipient_name"
            placeholder="Nome do destinatário"
            class="border p-2 rounded"
        >

        <input
            name="phone"
            placeholder="Telefone"
            class="border p-2 rounded"
        >

        <input
            name="cep"
            placeholder="CEP"
            class="border p-2 rounded"
        >

        <input
            name="street"
            placeholder="Rua"
            class="border p-2 rounded"
        >

        <input
            name="number"
            placeholder="Número"
            class="border p-2 rounded"
        >

        <input
            name="neighborhood"
            placeholder="Bairro"
            class="border p-2 rounded"
        >

        <input
            name="city"
            placeholder="Cidade"
            class="border p-2 rounded"
        >

        <input
            name="state"
            placeholder="Estado"
            class="border p-2 rounded"
        >

        <input
            name="complement"
            placeholder="Complemento"
            class="border p-2 rounded"
        >

    </div>

    <button class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded mt-4">
        Salvar Endereço
    </button>

</form>
