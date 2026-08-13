<header class="border-b border-[#eee6e4] bg-white">
    <div class="bg-[#fbe9e7] py-2">
        <div class="store-container flex items-center justify-between gap-3 text-[9px] font-semibold tracking-wide text-stone-700">
            <span>◌ Frete grátis acima de R$299</span>
            <span class="hidden sm:inline">↻ Troca fácil em até 7 dias</span>
            <a href="https://wa.me/5511931494708" target="_blank" class="hidden sm:inline hover:text-[#bd5564]">◉ Atendimento via WhatsApp</a>
        </div>
    </div>
    <div class="store-container relative flex min-h-18 items-center justify-between gap-5 py-4">
        <button type="button" aria-label="Abrir menu" class="p-1 text-stone-700 md:hidden"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
        <a href="{{ route('home') }}" class="store-title absolute left-1/2 shrink-0 -translate-x-1/2 text-2xl font-semibold tracking-[-.07em] text-stone-900 md:static md:translate-x-0">MALU <span class="font-normal">STORE</span></a>
        <nav class="hidden items-center gap-6 text-xs font-medium text-stone-700 md:flex">
            <a href="{{ route('catalog.index') }}" class="transition hover:text-[#bd5564]">Novidades</a>
            <a href="{{ route('catalog.index', ['category' => 'vestidos']) }}" class="transition hover:text-[#bd5564]">Vestidos</a>
            <a href="{{ route('catalog.index', ['category' => 'conjuntos']) }}" class="transition hover:text-[#bd5564]">Conjuntos</a>
            <a href="{{ route('catalog.index', ['category' => 'blusas']) }}" class="transition hover:text-[#bd5564]">Blusas</a>
            <a href="{{ route('catalog.index', ['category' => 'calcas']) }}" class="transition hover:text-[#bd5564]">Calças</a>
        </nav>
        <nav class="flex items-center gap-3 text-stone-700">
            <a href="{{ route('home') }}" aria-label="Buscar" class="p-1 transition hover:text-[#bd5564]"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><circle cx="11" cy="11" r="5.5"/><path d="m16 16 4 4"/></svg></a>
            <a href="#" aria-label="Favoritos" class="hidden p-1 transition hover:text-[#bd5564] sm:block"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.9-8.6a5.5 5.5 0 0 0-.1-7.8Z"/></svg></a>
            <div class="hidden md:block">@include('components.public.profile.profile-menu')</div>
            <a href="{{ route('public.cart.index') }}" aria-label="Sacola" class="relative p-1 transition hover:text-[#bd5564]"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path d="M5 8h14l-1 12H6L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg><span class="absolute -right-1 -top-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-[#d66f7c] text-[8px] text-white">2</span></a>
        </nav>
    </div>
</header>
