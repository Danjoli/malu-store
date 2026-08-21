<section class="checkout-step" aria-labelledby="checkout-shipping-title">
    <div id="checkout-shipping-title">
        <x-public.checkout.step-heading number="3" title="Forma de entrega" description="Calcule o frete e escolha uma opção dos Correios para o CEP selecionado." />
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="flex-1">
            <label for="shipping-cep-preview" class="mb-1.5 block text-xs font-semibold text-stone-700">CEP de entrega</label>
            <input type="text" id="shipping-cep-preview" class="store-input bg-stone-50" readonly aria-describedby="shipping-help">
        </div>
        <button type="button" id="btn-calcular-frete" class="store-button store-button-outline sm:min-w-44">Calcular frete</button>
    </div>
    <p id="shipping-help" class="mt-2 text-xs text-stone-500">Ao alterar o endereço ou CEP, escolha uma nova opção de entrega.</p>

    <div id="shipping-status" class="mt-4 rounded-md border border-[#eadfdd] bg-[#faf7f6] p-4 text-sm text-stone-500" role="status">
        Informe um CEP válido e calcule o frete.
    </div>
    <div id="fretes-container" class="mt-4 hidden">
        <fieldset>
            <legend class="mb-3 text-sm font-bold text-stone-800">Opções disponíveis</legend>
            <div id="lista-fretes" class="space-y-3"></div>
        </fieldset>
    </div>
    <p id="shipping-error" class="mt-3 hidden text-sm font-semibold text-red-600" role="alert">Selecione uma opção de entrega válida antes de continuar.</p>

    <input type="hidden" name="shipping_cost" id="shipping_cost" value="{{ old('shipping_cost') }}">
    <input type="hidden" name="carrier" id="carrier" value="{{ old('carrier') }}">
    <input type="hidden" name="service" id="service" value="{{ old('service') }}">
</section>
