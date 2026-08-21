@props(['number', 'title', 'description'])

<div class="mb-5 flex gap-3">
    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#d66f7c] text-xs font-bold text-white" aria-hidden="true">{{ $number }}</span>
    <div>
        <h2 class="store-title text-2xl font-semibold text-stone-900">{{ $title }}</h2>
        <p class="mt-1 text-xs leading-relaxed text-stone-500">{{ $description }}</p>
    </div>
</div>
