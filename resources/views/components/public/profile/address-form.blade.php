<h3 class="store-title mb-4 text-xl font-semibold">
    Adicionar novo endereço
</h3>

<form
    method="POST"
    action="{{ route('addresses.store') }}"
>
    @csrf

    <x-public.address-fields :show-default="true" />

    <button
        type="submit"
        class="store-button store-button-primary mt-4"
    >
        Salvar Endereço
    </button>
</form>
