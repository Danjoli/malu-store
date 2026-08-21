@props(['address' => null])

<section class="checkout-step" aria-labelledby="checkout-customer-title">
    <div id="checkout-customer-title">
        <x-public.checkout.step-heading number="2" title="Dados do pedido" description="Confirme os dados necessários para concluir a compra." />
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="cpf" class="mb-1.5 block text-xs font-semibold text-stone-700">CPF</label>
            <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" class="store-input" maxlength="14" inputmode="numeric" required>
        </div>
        <div>
            <label for="order-phone-preview" class="mb-1.5 block text-xs font-semibold text-stone-700">Telefone</label>
            <input type="text" id="order-phone-preview" value="{{ old('phone', $address->phone ?? '') }}" class="store-input bg-stone-50" readonly>
        </div>
    </div>
    <p class="mt-3 rounded-md bg-[#faf7f6] p-3 text-xs leading-relaxed text-stone-500">Usaremos esses dados apenas para processar o pedido, o pagamento e manter você informado sobre a entrega.</p>
</section>
