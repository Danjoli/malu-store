@props([
    'label',
    'value',
    'caption',
    'tone' => 'neutral',
])

@php
    $tones = [
        'neutral' => 'bg-[#f8f3f1] text-[#746b68]',
        'rose' => 'bg-[#fdf0f3] text-[#b85d70]',
    ];
@endphp

<article class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
    <p class="text-sm font-medium text-[#746b68]">{{ $label }}</p>
    <p class="mt-3 text-2xl font-bold tracking-tight text-[#2d2928]">{{ $value }}</p>
    <span class="mt-4 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $tones[$tone] ?? $tones['neutral'] }}">
        {{ $caption }}
    </span>
</article>
