<form
    id="cardPaymentForm"
    method="POST"
    action="{{ route('payment.card.process', $order->id) }}"
    >

    @csrf

    {{-- Número do cartão --}}
    <div class="mb-4">
        <label for="card_number"
            class="block text-sm font-medium text-gray-700 mb-1">
            Número do cartão
        </label>

        <input
            type="text"
            id="card_number"
            name="card_number"
            maxlength="19"
            placeholder="0000 0000 0000 0000"
            class="w-full border rounded-lg px-4 py-3"
            required
        >
    </div>

    {{-- Nome do titular --}}
    <div class="mb-4">
        <label for="holder_name"
            class="block text-sm font-medium text-gray-700 mb-1">
            Nome no cartão
        </label>

        <input
            type="text"
            id="holder_name"
            name="holder_name"
            placeholder="Nome como aparece no cartão"
            class="w-full border rounded-lg px-4 py-3"
            required
        >
    </div>

    {{-- CPF --}}
    <div class="mb-4">
        <label for="cpf"
            class="block text-sm font-medium text-gray-700 mb-1">
            CPF do titular
        </label>

        <input
            type="text"
            id="cpf"
            name="cpf"
            maxlength="14"
            placeholder="000.000.000-00"
            class="w-full border rounded-lg px-4 py-3"
            required
        >
    </div>

    {{-- Validade --}}
    <div class="grid grid-cols-2 gap-4 mb-4">

        <div>
            <label for="expiration_month"
                class="block text-sm font-medium text-gray-700 mb-1">
                Mês
            </label>

            <select
                id="expiration_month"
                name="expiration_month"
                class="w-full border rounded-lg px-4 py-3"
                required
            >
                <option value="">Mês</option>

                @for($month = 1; $month <= 12; $month++)

                    <option value="{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}">
                        {{ str_pad($month, 2, '0', STR_PAD_LEFT) }}
                    </option>

                @endfor

            </select>
        </div>

        <div>
            <label for="expiration_year"
                class="block text-sm font-medium text-gray-700 mb-1">
                Ano
            </label>

            <select
                id="expiration_year"
                name="expiration_year"
                class="w-full border rounded-lg px-4 py-3"
                required
            >
                <option value="">Ano</option>

                @for($year = now()->year; $year <= now()->year + 15; $year++)

                    <option value="{{ $year }}">
                        {{ $year }}
                    </option>

                @endfor

            </select>
        </div>

    </div>

    {{-- CVV --}}
    <div class="mb-4">

        <label for="ccv"
            class="block text-sm font-medium text-gray-700 mb-1">
            CVV
        </label>

        <input
            type="text"
            id="ccv"
            name="ccv"
            maxlength="4"
            placeholder="123"
            class="w-full border rounded-lg px-4 py-3"
            required
        >

    </div>

    {{-- Total --}}
    <div class="border-t pt-4 mb-6">

        <div class="flex justify-between text-lg font-bold">

            <span>Total:</span>

            <span class="text-blue-600">
                R$ {{ number_format($order->total, 2, ',', '.') }}
            </span>

        </div>

    </div>

    {{-- Botão --}}
    <button
        type="submit"
        id="cardSubmitButton"
        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
    >
        Pagar com Cartão
    </button>

    </form>
