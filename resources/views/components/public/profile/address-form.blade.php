@props(['canAddAddress' => true])

@if ($canAddAddress)
    <h3 class="store-title mb-4 text-xl font-semibold">
        Adicionar novo endereço
    </h3>

    <form method="POST" action="{{ route('addresses.store') }}">
        @csrf

        <x-public.address-fields :show-default="true" />

        <button type="submit" class="store-button store-button-primary mt-4">
            Salvar Endereço
        </button>
    </form>
@else
    <div class="rounded-md border border-[#eaded9] bg-[#fff8f7] p-4 text-sm text-[#746b68]">
        Você atingiu o limite de 10 endereços. Remova um endereço para cadastrar outro.
    </div>
@endif
