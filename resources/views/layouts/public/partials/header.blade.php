<header class="store-header sticky top-0 z-50 border-b">
    @include('layouts.public.partials.header.top-bar')

    <div
        x-data="{
            searchOpen: false,
            menuOpen: false,
            dark: document.documentElement.classList.contains('dark'),
            toggleTheme() {
                this.dark = !this.dark;
                window.storeTheme.set(this.dark);
            }
        }"
        class="store-container relative flex min-h-18 items-center justify-between gap-5 py-[clamp(0.8rem,1vw,1.25rem)]"
    >
        <button
            type="button"
            @click="menuOpen = !menuOpen"
            :aria-expanded="menuOpen"
            aria-label="Abrir menu"
            class="p-1 text-stone-700 md:hidden"
        >
            <svg
                class="h-6 w-6"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                viewBox="0 0 24 24"
            >
                <path d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>

        <a
            href="{{ route('home') }}"
            class="absolute left-1/2 flex shrink-0 -translate-x-1/2 items-center gap-2.5 text-stone-900 md:static md:translate-x-0"
        >
            <span class="store-title flex h-9 w-9 items-center justify-center rounded-full bg-[var(--store-accent)] text-sm font-semibold tracking-[-.08em] text-[var(--store-accent-contrast)] shadow-sm">
                MS
            </span>
            <span class="store-title hidden text-[clamp(1.1rem,1.35vw,1.65rem)] font-semibold tracking-[-.06em] sm:inline">
                MALU <span class="font-normal italic">STORE</span>
            </span>
        </a>

        <nav class="hidden items-center gap-[clamp(1rem,1.6vw,2rem)] text-[clamp(0.65rem,0.65vw,0.9rem)] font-medium text-stone-700 md:flex">
            <a
                href="{{ route('catalog.index') }}"
                class="transition hover:text-[#bd5564]"
            >
                Novidades
            </a>

            <a
                href="{{ route('catalog.index', ['category' => 'vestidos']) }}"
                class="transition hover:text-[#bd5564]"
            >
                Vestidos
            </a>

            <a
                href="{{ route('catalog.index', ['category' => 'conjuntos']) }}"
                class="transition hover:text-[#bd5564]"
            >
                Conjuntos
            </a>

            <a
                href="{{ route('catalog.index', ['category' => 'blusas']) }}"
                class="transition hover:text-[#bd5564]"
            >
                Blusas
            </a>

            <a
                href="{{ route('catalog.index', ['category' => 'calcas']) }}"
                class="transition hover:text-[#bd5564]"
            >
                Calças
            </a>
        </nav>

        <nav
            x-show="menuOpen"
            x-cloak
            x-transition.origin.top.left
            class="absolute inset-x-0 top-full z-30 border border-[#eee6e4] bg-white p-4 shadow-lg md:hidden"
        >
            <div class="grid grid-cols-2 gap-2 text-sm font-semibold text-stone-700">
                <a
                    @click="menuOpen = false"
                    href="{{ route('catalog.index') }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]"
                >
                    Novidades
                </a>

                <a
                    @click="menuOpen = false"
                    href="{{ route('catalog.index', ['category' => 'vestidos']) }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]"
                >
                    Vestidos
                </a>

                <a
                    @click="menuOpen = false"
                    href="{{ route('catalog.index', ['category' => 'conjuntos']) }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]"
                >
                    Conjuntos
                </a>

                <a
                    @click="menuOpen = false"
                    href="{{ route('catalog.index', ['category' => 'blusas']) }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]"
                >
                    Blusas
                </a>

                <a
                    @click="menuOpen = false"
                    href="{{ route('catalog.index', ['category' => 'calcas']) }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]"
                >
                    Calças
                </a>

                <a
                    @click="menuOpen = false"
                    href="{{ auth()->check() ? route('favorites.index') : route('login') }}"
                    class="rounded-lg px-3 py-2 hover:bg-[#fdf0f3] hover:text-[#bd5564]"
                >
                    Favoritos
                </a>
            </div>
        </nav>

        <nav class="flex items-center gap-3 text-stone-700">
            <button
                type="button"
                @click="toggleTheme()"
                :aria-label="dark ? 'Ativar tema claro' : 'Ativar tema escuro'"
                :title="dark ? 'Tema claro' : 'Tema escuro'"
                class="theme-toggle flex h-9 w-9 items-center justify-center rounded-full transition"
            >
                <svg x-show="!dark" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.4 15.2A8.5 8.5 0 0 1 8.8 3.6 8.5 8.5 0 1 0 20.4 15.2Z" />
                </svg>
                <svg x-show="dark" x-cloak class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="3.5" />
                    <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
                </svg>
            </button>

            <button
                type="button"
                @click="searchOpen = !searchOpen; $nextTick(() => $refs.searchInput?.focus())"
                aria-label="Buscar"
                class="p-1 transition hover:text-[#bd5564]"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    viewBox="0 0 24 24"
                >
                    <circle cx="11" cy="11" r="5.5" />
                    <path d="m16 16 4 4" />
                </svg>
            </button>

            <a
                href="{{ auth()->check() ? route('favorites.index') : route('login') }}"
                aria-label="Favoritos"
                class="relative hidden p-1 transition hover:text-[#bd5564] sm:block"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    viewBox="0 0 24 24"
                >
                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.9-8.6a5.5 5.5 0 0 0-.1-7.8Z" />
                </svg>

                @if (($favoritesCount ?? 0) > 0)
                    <span
                        class="absolute right-0 top-0 flex h-4 w-4 translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-[#d66f7c] text-[9px] font-semibold leading-none text-white"
                    >
                        {{ min($favoritesCount, 99) }}
                    </span>
                @endif
            </a>

            <div class="hidden md:block">
                @include('components.public.profile.profile-menu')
            </div>

            <a
                href="{{ auth()->check() ? route('public.cart.index') : route('login') }}"
                aria-label="Sacola"
                class="relative p-1 transition hover:text-[#bd5564]"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.6"
                    viewBox="0 0 24 24"
                >
                    <path d="M5 8h14l-1 12H6L5 8Z" />
                    <path d="M9 9V6a3 3 0 0 1 6 0v3" />
                </svg>

                @if (($cartItemCount ?? 0) > 0)
                    <span
                        class="absolute right-0 top-0 flex h-4 w-4 translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-[#d66f7c] text-[9px] font-semibold leading-none text-white"
                    >
                        {{ min($cartItemCount, 99) }}
                    </span>
                @endif
            </a>
        </nav>

        <form
            x-show="searchOpen"
            x-cloak
            x-transition
            action="{{ route('catalog.index') }}"
            method="GET"
            class="store-panel absolute inset-x-0 top-full z-30 border p-3 shadow-lg"
        >
            <div class="relative">
                <input
                    x-ref="searchInput"
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Buscar produto..."
                    class="store-input pr-20"
                >

                <button class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-bold text-[#bd5564]">
                    Buscar
                </button>
            </div>
        </form>
    </div>
</header>
