<form
    id="cardPaymentForm"
    method="POST"
    action="{{ route('payment.card.process', $order->id) }}"
    >

    @csrf

    {{-- Número do cartão --}}
    <div class="mb-4">
        <label for="card_number"
            class="mb-2 block text-sm font-semibold text-[#443d3b]">
            Número do cartão
        </label>

        <input
            type="text"
            id="card_number"
            name="card_number"
            maxlength="19"
            placeholder="0000 0000 0000 0000"
            class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
            required
        >
    </div>

    {{-- Nome do titular --}}
    <div class="mb-4">
        <label for="holder_name"
            class="mb-2 block text-sm font-semibold text-[#443d3b]">
            Nome no cartão
        </label>

        <input
            type="text"
            id="holder_name"
            name="holder_name"
            placeholder="Nome como aparece no cartão"
            class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
            required
        >
    </div>

    {{-- CPF --}}
    <div class="mb-4">
        <label for="cpf"
            class="mb-2 block text-sm font-semibold text-[#443d3b]">
            CPF do titular
        </label>

        <input
            type="text"
            id="cpf"
            name="cpf"
            maxlength="14"
            placeholder="000.000.000-00"
            class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
            required
        >
    </div>

    {{-- Validade --}}
    <div class="grid grid-cols-2 gap-4 mb-4">

        <div>
            <label for="expiration_month"
                class="mb-2 block text-sm font-semibold text-[#443d3b]">
                Mês
            </label>

            <select
                id="expiration_month"
                name="expiration_month"
                class="w-full rounded-xl border border-[#ded4d0] bg-white px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
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
                class="mb-2 block text-sm font-semibold text-[#443d3b]">
                Ano
            </label>

            <select
                id="expiration_year"
                name="expiration_year"
                class="w-full rounded-xl border border-[#ded4d0] bg-white px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
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
            class="mb-2 block text-sm font-semibold text-[#443d3b]">
            CVV
        </label>

        <input
            type="text"
            id="ccv"
            name="ccv"
            maxlength="4"
            placeholder="123"
            class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
            required
        >

    </div>

    {{-- Total --}}
    <div class="mb-6 border-t border-[#f0e5e1] pt-5">

        <div class="flex justify-between text-lg font-bold">

            <span>Total:</span>

            <span class="text-[#b85d70]">
                R$ {{ number_format($order->total, 2, ',', '.') }}
            </span>

        </div>

    </div>

    {{-- Botão --}}
    <button
        type="submit"
        id="cardSubmitButton"
        class="w-full rounded-xl bg-[#cf7184] py-3.5 text-sm font-bold text-white transition hover:bg-[#b85d70]"
    >
        Pagar com Cartão
    </button>

    </form>
