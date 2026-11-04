@php($storeSettings = \App\Models\StoreSetting::current())
<div class="bg-[var(--store-surface-accent)] py-2">
    <div class="store-container flex items-center justify-between gap-3 text-[clamp(0.55rem,0.55vw,0.78rem)] font-semibold tracking-wide text-stone-700">
        <span>◌ {{ $storeSettings->announcement ?: 'Frete grátis acima de R$'.number_format((float) $storeSettings->free_shipping_threshold, 0, ',', '.') }}</span>

        <span class="hidden sm:inline">↻ Troca fácil em até 7 dias</span>

        <a
            href="https://wa.me/{{ preg_replace('/\D+/', '', $storeSettings->whatsapp ?: '5511931494708') }}"
            target="_blank"
            rel="noopener noreferrer"
            class="store-link hidden sm:inline"
        >
            ◉ Atendimento via WhatsApp
        </a>
    </div>
</div>
