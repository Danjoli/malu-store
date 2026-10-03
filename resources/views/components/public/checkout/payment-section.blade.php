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

    <p class="mt-4 rounded-md border border-[#f0e4e1] bg-[#fffaf9] p-4 text-xs leading-5 text-stone-600">
        Ao escolher cartão, você será direcionado ao ambiente seguro do Asaas. A Malu Store não recebe nem armazena o número ou o código de segurança do cartão.
    </p>
</section>
