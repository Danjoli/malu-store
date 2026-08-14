@props([
    'code',
    'eyebrow' => 'Algo não saiu como esperado',
    'title',
    'message',
    'actionLabel' => 'Voltar para a loja',
    'actionUrl' => null,
])

<section class="store-container flex min-h-[58vh] items-center justify-center py-12 text-center sm:py-20">
    <div class="w-full max-w-xl rounded-2xl border border-[#eaded9] bg-white px-6 py-10 shadow-[0_12px_34px_rgba(76,50,47,0.08)] sm:px-10">
        <p class="store-kicker text-[#bd5564]">
            {{ $eyebrow }}
        </p>

        <p class="mt-4 font-['Cormorant_Garamond'] text-7xl font-semibold leading-none text-[#e8cbd1] sm:text-8xl">
            {{ $code }}
        </p>

        <h1 class="store-title mt-5 text-4xl text-stone-900 sm:text-5xl">
            {{ $title }}
        </h1>

        <p class="mx-auto mt-4 max-w-md text-sm leading-6 text-stone-600 sm:text-base">
            {{ $message }}
        </p>

        @if ($actionUrl)
            <a
                href="{{ $actionUrl }}"
                class="store-button store-button-primary mt-7"
            >
                {{ $actionLabel }}
            </a>
        @endif

        @if (isset($slot) && $slot->isNotEmpty())
            <div class="mt-4">
                {{ $slot }}
            </div>
        @endif
    </div>
</section>
