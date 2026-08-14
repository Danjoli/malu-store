<div
    {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_8px_24px_rgba(76,50,47,0.05)]']) }}>
    <div class="overflow-x-auto">{{ $slot }}</div>
</div>
