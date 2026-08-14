@props(['eyebrow', 'title', 'description'])

<section class="store-container py-10 sm:py-14 lg:py-18">
    <div class="mx-auto max-w-4xl">
        <div class="border-b border-[#eaded9] pb-7 sm:pb-9">
            <p class="store-kicker text-[#bd5564]">{{ $eyebrow }}</p>
            <h1 class="store-title mt-3 text-4xl leading-tight text-stone-900 sm:text-5xl">{{ $title }}</h1>
            <p class="mt-4 max-w-2xl text-sm leading-6 text-stone-600 sm:text-base">{{ $description }}</p>
        </div>

        <article class="legal-document mt-8 rounded-2xl border border-[#eaded9] bg-white p-6 shadow-[0_8px_24px_rgba(76,50,47,0.05)] sm:p-9">
            {{ $slot }}
        </article>
    </div>
</section>
