@props(['addresses' => [], 'address' => null])

<section class="checkout-step" aria-labelledby="checkout-address-title">
    <div id="checkout-address-title">
        <x-public.checkout.step-heading number="1" title="Endereço de entrega" description="Escolha onde deseja receber o seu pedido." />
    </div>

    <input type="hidden" name="address_id" id="address_id" value="{{ old('address_id', $address->id ?? '') }}">

    <div class="grid gap-3 sm:grid-cols-2 {{ count($addresses) ? '' : 'hidden' }}" id="address-options">
        @if (count($addresses))
            @foreach ($addresses as $option)
                <label class="address-card relative cursor-pointer rounded-md border bg-white p-4 transition" data-address-id="{{ $option['id'] }}">
                    <input type="radio" name="address_choice" value="{{ $option['id'] }}" class="peer sr-only" @checked((string) old('address_id', $address->id ?? '') === (string) $option['id'])>
                    <span class="absolute right-4 top-4 h-4 w-4 rounded-full border border-stone-300 peer-checked:border-[5px] peer-checked:border-[#d66f7c]"></span>
                    <span class="block pr-7 text-sm font-bold text-stone-800">{{ $option['label'] ?: 'Endereço' }}</span>
                    <span class="mt-2 block text-xs leading-5 text-stone-500">
                        {{ $option['street'] }}, {{ $option['number'] }}<br>
                        {{ $option['neighborhood'] }} · {{ $option['city'] }}/{{ $option['state'] }}<br>
                        CEP {{ $option['cep'] }}
                    </span>
                </label>
            @endforeach
        @endif
    </div>

    <div class="mt-4 flex flex-wrap gap-4 text-xs font-semibold">
        <button type="button" id="btn-new-address" class="text-[#bd5564] hover:underline">+ Adicionar novo endereço</button>
        @if ($address)
            <button type="button" id="btn-edit-address" class="text-stone-600 hover:text-[#bd5564] hover:underline">Editar endereço selecionado</button>
        @endif
        <a href="{{ route('profile.edit') }}" class="text-stone-600 hover:text-[#bd5564] hover:underline">Gerenciar endereços</a>
    </div>

    <div id="address-editor" class="mt-5 rounded-md border border-[#f0e4e1] bg-[#fffaf9] p-4 sm:p-5 {{ $errors->hasAny(['label','recipient_name','phone','cep','street','number','complement','neighborhood','city','state']) ? '' : 'hidden' }}">
        <div class="mb-4 flex items-center justify-between gap-4">
            <h3 id="address-editor-title" class="text-sm font-bold text-stone-800">Editar endereço</h3>
            <button type="button" id="btn-close-address-editor" class="text-xs font-semibold text-stone-500 hover:text-stone-800">Fechar</button>
        </div>
        <x-public.address-fields :address="$address" />
        <div id="new-address-actions" class="mt-5 hidden border-t border-[#eadfdd] pt-4">
            <button type="button" id="btn-save-new-address" class="store-button store-button-primary">Salvar e usar este endereço</button>
            <p id="new-address-feedback" class="mt-3 hidden text-sm" role="status"></p>
        </div>
    </div>
</section>
