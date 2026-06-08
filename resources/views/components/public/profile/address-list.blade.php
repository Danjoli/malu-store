@props(['addresses'])

<h2 class="text-xl font-semibold mb-6">
    Meus Endereços
</h2>

@forelse ($addresses as $address)

    <div class="border rounded-lg p-4 mb-4">

        <div class="font-semibold text-gray-800">
            {{ $address->label }}
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

        <form
            method="POST"
            action="{{ route('profile.address.delete', $address->id) }}"
            class="mt-3"
        >

            @csrf
            @method('DELETE')

            <button class="text-red-500 text-sm hover:underline">
                Excluir endereço
            </button>

        </form>

    </div>

@empty

    <p class="text-gray-500 mb-4">
        Nenhum endereço cadastrado.
    </p>

@endforelse
