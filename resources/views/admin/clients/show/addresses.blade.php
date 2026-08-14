{{-- ENDEREÇOS --}}

@if ($addresses->count())
    <div class="border-t pt-6">

        <p class="mb-3 text-lg font-semibold">
            Endereços do Cliente
        </p>

        <div class="space-y-3">
            @foreach ($addresses as $address)
                <div class="rounded-lg border bg-gray-50 p-4">

                    <p class="font-semibold">
                        {{ $address->label ?? 'Endereço' }}
                    </p>

                    <p class="text-sm text-gray-600">
                        {{ $address->street }}, {{ $address->number }}
                    </p>

                    @if ($address->complement)
                        <p class="text-sm text-gray-600">
                            {{ $address->complement }}
                        </p>
                    @endif

                    <p class="text-sm text-gray-600">
                        {{ $address->neighborhood }}
                    </p>

                    <p class="text-sm text-gray-600">
                        {{ $address->city }} - {{ $address->state }}
                    </p>

                    <p class="text-sm text-gray-600">
                        CEP: {{ $address->cep }}
                    </p>

                </div>
            @endforeach
        </div>

    </div>
@endif
