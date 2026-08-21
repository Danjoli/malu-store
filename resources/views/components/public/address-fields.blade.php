@props([
    'address' => null,
    'showDefault' => false,
    'required' => true,
    'idSuffix' => '',
])

@php
    $fields = [
        'label' => ['Nome do endereço', 'Ex.: Casa ou Trabalho', false],
        'recipient_name' => ['Nome do destinatário', 'Quem receberá o pedido', true],
        'phone' => ['Telefone', '(00) 00000-0000', true],
        'cep' => ['CEP', '00000-000', true],
        'street' => ['Rua', 'Nome da rua', true],
        'number' => ['Número', 'Número ou S/N', true],
        'complement' => ['Complemento', 'Apartamento, bloco, referência', false],
        'neighborhood' => ['Bairro', 'Bairro', true],
        'city' => ['Cidade', 'Cidade', true],
        'state' => ['Estado', 'UF', true],
    ];
@endphp

<div {{ $attributes->class(['grid gap-4 sm:grid-cols-2']) }}>
    @foreach ($fields as $name => [$label, $placeholder, $isRequired])
        <div @class([
            'sm:col-span-2' => in_array($name, ['street', 'complement']),
        ])>
            <label for="{{ $name }}{{ $idSuffix }}" class="mb-1.5 block text-xs font-semibold text-stone-700">
                {{ $label }}
                @if (! $isRequired)
                    <span class="font-normal text-stone-400">(opcional)</span>
                @endif
            </label>

            <input
                type="text"
                id="{{ $name }}{{ $idSuffix }}"
                name="{{ $name }}"
                value="{{ old($name, data_get($address, $name, '')) }}"
                placeholder="{{ $placeholder }}"
                @if ($name === 'state') maxlength="2" @endif
                @if ($name === 'cep') inputmode="numeric" autocomplete="postal-code" @endif
                @if ($name === 'phone') inputmode="tel" autocomplete="tel" @endif
                @if ($required && $isRequired) required @endif
                @class(['store-input', 'uppercase' => $name === 'state'])
            >
        </div>
    @endforeach

    @if ($showDefault)
        <label class="flex items-center gap-2 text-sm text-stone-600 sm:col-span-2">
            <input type="checkbox" name="is_default" value="1" @checked(old('is_default', data_get($address, 'is_default', false))) class="accent-brand-500">
            Usar como endereço principal
        </label>
    @endif
</div>
