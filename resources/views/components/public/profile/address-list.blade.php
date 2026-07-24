@props(['addresses'])

<h2 class="text-xl font-semibold mb-6">
    Meus Endereços
</h2>

@forelse ($addresses as $address)

    <div class="border rounded-lg p-4 mb-4">

        <div class="flex items-center justify-between mb-2">

            <div class="font-semibold text-gray-800">
                {{ $address->label ?: 'Endereço' }}
            </div>

            @if ($address->is_default)
                <span class="text-sm font-semibold text-green-600">
                    Endereço principal
                </span>
            @endif

        </div>

        <p class="text-sm text-gray-600">
            {{ $address->street }}, {{ $address->number }}
        </p>

        <p class="text-sm text-gray-600">
            {{ $address->neighborhood }}
        </p>

        <p class="text-sm text-gray-600">
            {{ $address->city }} - {{ $address->state }}
        </p>

        <p class="text-sm text-gray-600">
            CEP: {{ $address->cep }}
        </p>

        <div class="flex items-center gap-4 mt-4">

            @if (!$address->is_default)

                <form
                    method="POST"
                    action="{{ route('profile.address.default', $address->id) }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="text-blue-600 text-sm hover:underline"
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
                    class="text-red-500 text-sm hover:underline"
                >
                    Excluir endereço
                </button>

            </form>

        </div>

    </div>

@empty

    <p class="text-gray-500 mb-4">
        Nenhum endereço cadastrado.
    </p>

@endforelse
