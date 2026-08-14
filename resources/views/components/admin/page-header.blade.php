@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<div
    {{ $attributes->merge([
        'class' => 'mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between',
    ]) }}
>
    <div>
        @if ($eyebrow)
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">
                {{ $eyebrow }}
            </p>
        @endif

        <h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold text-[#2d2928]">
            {{ $title }}
        </h1>

        @if ($description)
            <p class="mt-1 text-sm text-[#746b68]">
                {{ $description }}
            </p>
        @endif
    </div>

    @if (isset($actions))
        <div class="shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
