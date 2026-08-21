@props(['addresses'])

<h2 class="store-title mb-6 text-2xl font-semibold">
    Meus Endereços
</h2>

@forelse ($addresses as $address)
    <div class="mb-4 rounded-md border border-[#eadfdd] bg-[#fffaf9] p-5">
        <div class="mb-2 flex items-center justify-between">
            <div class="font-semibold text-stone-800">
                {{ $address->label ?: 'Endereço' }}
            </div>

            @if ($address->is_default)
                <span class="text-xs font-semibold text-emerald-700">
                    Endereço principal
                </span>
            @endif
        </div>

        <p class="text-sm text-stone-600">
            {{ $address->street }}, {{ $address->number }}
        </p>

        <p class="text-sm text-stone-600">
            {{ $address->neighborhood }}
        </p>

        <p class="text-sm text-stone-600">
            {{ $address->city }} - {{ $address->state }}
        </p>

        <p class="text-sm text-stone-600">
            CEP: {{ $address->cep }}
        </p>

        <div class="mt-4 flex flex-wrap items-center gap-4">
            <button type="button" class="text-sm text-[#bd5564] hover:underline" x-data @click="$dispatch('toggle-address-edit', { id: {{ $address->id }} })">
                Editar endereço
            </button>
            @if (!$address->is_default)
                <form
                    method="POST"
                    action="{{ route('profile.address.default', $address->id) }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="text-sm text-blue-600 hover:underline"
                    >
                        Definir como principal
                    </button>
                </form>
            @endif

            <form
                method="POST"
                action="{{ route('profile.address.delete', $address->id) }}"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="text-sm text-red-500 hover:underline"
                >
                    Excluir endereço
                </button>
            </form>
        </div>

        <div x-data="{ open: false }" @toggle-address-edit.window="if ($event.detail.id === {{ $address->id }}) open = !open" x-show="open" x-cloak class="mt-5 border-t border-[#eadfdd] pt-5">
            <form method="POST" action="{{ route('addresses.update', $address->id) }}">
                @csrf
                @method('PUT')
                <x-public.address-fields :address="$address" :show-default="true" :id-suffix="'-edit-'.$address->id" />
                <div class="mt-4 flex gap-3">
                    <button type="submit" class="store-button store-button-primary">Salvar alterações</button>
                    <button type="button" class="store-button store-button-outline" @click="open = false">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
@empty
    <p class="mb-4 text-gray-500">
        Nenhum endereço cadastrado.
    </p>
@endforelse
