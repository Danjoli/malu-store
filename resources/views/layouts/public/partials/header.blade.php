<header class="relative z-50 border-b border-[#eee6e4] bg-white">
    <div class="bg-[#fbe9e7] py-2">
        <div
            class="store-container flex items-center justify-between gap-3 text-[clamp(0.55rem,0.55vw,0.78rem)] font-semibold tracking-wide text-stone-700">
            <span>◌ Frete grátis acima de R$299</span>
            <span class="hidden sm:inline">↻ Troca fácil em até 7 dias</span>
            <a href="https://wa.me/5511931494708" target="_blank" class="hidden sm:inline hover:text-[#bd5564]">◉
                Atendimento via WhatsApp</a>
        </div>
    </div>
    <div x-data="{ searchOpen: false, menuOpen: false }"
        class="store-container relative flex min-h-18 items-center justify-between gap-5 py-[clamp(0.8rem,1vw,1.25rem)]">
        <button type="button" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen" aria-label="Abrir menu"
            class="p-1 text-stone-700 md:hidden"><svg class="h-6 w-6" fill="none" stroke="currentColor"
                stroke-width="1.6" viewBox="0 0 24 24">
                <path d="M4 7h16M4 12h16M4 17h16" />
            </svg></button>
        <a href="{{ route('home') }}"
            class="store-title absolute left-1/2 shrink-0 -translate-x-1/2 text-[clamp(1.25rem,1.6vw,2.5rem)] font-semibold tracking-[-.07em] text-stone-900 md:static md:translate-x-0">MALU
            <span class="font-normal">STORE</span></a>
        <nav
            class="hidden items-center gap-[clamp(1rem,1.6vw,2rem)] text-[clamp(0.65rem,0.65vw,0.9rem)] font-medium text-stone-700 md:flex">
            <a href="{{ route('catalog.index') }}" class="transition hover:text-[#bd5564]">Novidades</a>
            <a href="{{ route('catalog.index', ['category' => 'vestidos']) }}"
                class="transition hover:text-[#bd5564]">Vestidos</a>
            <a href="{{ route('catalog.index', ['category' => 'conjuntos']) }}"
                class="transition hover:text-[#bd5564]">Conjuntos</a>
            <a href="{{ route('catalog.index', ['category' => 'blusas']) }}"
                class="transition hover:text-[#bd5564]">Blusas</a>
            <a href="{{ route('catalog.index', ['category' => 'calcas']) }}"
                class="transition hover:text-[#bd5564]">Calças</a>
        </nav>
        <nav x-show="menuOpen" x-cloak x-transition.origin.top.left
            class="absolute inset-x-0 top-full z-30 border border-[#eee6e4] bg-white p-4 shadow-lg md:hidden">
            <div class="grid grid-cols-2 gap-2 text-sm font-semibold text-stone-700">
                <a @click="menuOpen = false" href="{{ route('catalog.index') }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]">Novidades</a>
                <a @click="menuOpen = false" href="{{ route('catalog.index', ['category' => 'vestidos']) }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]">Vestidos</a>
                <a @click="menuOpen = false" href="{{ route('catalog.index', ['category' => 'conjuntos']) }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]">Conjuntos</a>
                <a @click="menuOpen = false" href="{{ route('catalog.index', ['category' => 'blusas']) }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]">Blusas</a>
                <a @click="menuOpen = false" href="{{ route('catalog.index', ['category' => 'calcas']) }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]">Calças</a>
                <a @click="menuOpen = false" href="{{ auth()->check() ? route('favorites.index') : route('login') }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]">Favoritos</a>
            </div>
        </nav>
        <nav class="flex items-center gap-3 text-stone-700">
            <button type="button" @click="searchOpen = !searchOpen; $nextTick(() => $refs.searchInput?.focus())"
                aria-label="Buscar" class="p-1 transition hover:text-[#bd5564]"><svg class="h-5 w-5" fill="none"
                    stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="5.5" />
                    <path d="m16 16 4 4" />
                </svg></button>
            <a href="{{ auth()->check() ? route('favorites.index') : route('login') }}" aria-label="Favoritos"
                class="hidden p-1 transition hover:text-[#bd5564] sm:block"><svg class="h-5 w-5" fill="none"
                    stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path
                        d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.9-8.6a5.5 5.5 0 0 0-.1-7.8Z" />
                </svg></a>
            <div class="hidden md:block">@include('components.public.profile.profile-menu')</div>
            <a href="{{ auth()->check() ? route('public.cart.index') : route('login') }}" aria-label="Sacola"
                class="relative p-1 transition hover:text-[#bd5564]"><svg class="h-5 w-5" fill="none"
                    stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                    <path d="M5 8h14l-1 12H6L5 8Z" />
                    <path d="M9 9V6a3 3 0 0 1 6 0v3" />
                </svg>
                @if (($cartItemCount ?? 0) > 0)
                    <span
                        class="absolute -right-1 -top-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-[#d66f7c] text-[8px] text-white">{{ min($cartItemCount, 99) }}</span>
                @endif
            </a>
        </nav>
        <form x-show="searchOpen" x-cloak x-transition action="{{ route('catalog.index') }}" method="GET"
            class="absolute inset-x-0 top-full z-30 border border-[#eee6e4] bg-white p-3 shadow-lg">
            <div class="relative"><input x-ref="searchInput" type="search" name="search"
                    placeholder="Buscar produto..." class="store-input pr-20" value="{{ request('search') }}"><button
                    class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-bold text-[#bd5564]">Buscar</button>
            </div>
        </form>
    </div>
</header>
