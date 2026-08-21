<section class="checkout-step" aria-labelledby="checkout-payment-title">
    <div id="checkout-payment-title">
        <x-public.checkout.step-heading number="4" title="Forma de pagamento" description="Escolha como deseja pagar. Você seguirá direto para a confirmação." />
    </div>

    <fieldset class="space-y-3">
        <legend class="sr-only">Método de pagamento</legend>
        @foreach (\App\Enums\PaymentMethod::cases() as $paymentMethod)
            <label class="payment-card flex cursor-pointer items-center gap-4 rounded-md border border-[#eadfdd] bg-white p-4 transition hover:border-[#d66f7c]">
                <input type="radio" name="payment_method" value="{{ $paymentMethod->value }}" @checked(old('payment_method', \App\Enums\PaymentMethod::Pix->value) === $paymentMethod->value) class="h-4 w-4 accent-[#d66f7c]" required>
                <span class="flex h-9 w-11 items-center justify-center rounded bg-[#fff1f0] text-[10px] font-bold text-[#bd5564]">{{ $paymentMethod->badge() }}</span>
                <span class="flex-1">
                    <span class="block text-sm font-bold text-stone-800">{{ $paymentMethod->label() }}</span>
                    <span class="mt-1 block text-xs text-stone-500">{{ $paymentMethod->description() }}</span>
                </span>
            </label>
        @endforeach
    </fieldset>

    <div id="card-fields" class="mt-4 hidden rounded-md border border-[#f0e4e1] bg-[#fffaf9] p-4 sm:p-5">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2"><label for="card_number" class="mb-1.5 block text-xs font-semibold">Número do cartão</label><input class="store-input" id="card_number" name="card_number" maxlength="19" inputmode="numeric" autocomplete="cc-number" value="{{ old('card_number') }}"></div>
            <div class="sm:col-span-2"><label for="holder_name" class="mb-1.5 block text-xs font-semibold">Nome no cartão</label><input class="store-input" id="holder_name" name="holder_name" autocomplete="cc-name" value="{{ old('holder_name') }}"></div>
            <div><label for="expiration_month" class="mb-1.5 block text-xs font-semibold">Mês</label><input class="store-input" id="expiration_month" name="expiration_month" maxlength="2" inputmode="numeric" autocomplete="cc-exp-month" value="{{ old('expiration_month') }}"></div>
            <div><label for="expiration_year" class="mb-1.5 block text-xs font-semibold">Ano</label><input class="store-input" id="expiration_year" name="expiration_year" maxlength="4" inputmode="numeric" autocomplete="cc-exp-year" value="{{ old('expiration_year') }}"></div>
            <div><label for="ccv" class="mb-1.5 block text-xs font-semibold">CVV</label><input class="store-input" id="ccv" name="ccv" maxlength="4" inputmode="numeric" autocomplete="cc-csc" value="{{ old('ccv') }}"></div>
        </div>
    </div>
</section>
