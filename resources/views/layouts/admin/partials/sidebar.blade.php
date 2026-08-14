<aside
    :class="{
        'translate-x-0': adminMenuOpen,
        '-translate-x-full': !adminMenuOpen,
        'lg:hidden': sidebarCollapsed,
    }"
    class="fixed inset-y-0 left-0 z-50 flex h-screen w-72 flex-col justify-between border-r border-[#4c3d3b] bg-[#2d2928] p-5 text-[#fdf8f6] shadow-[10px_0_30px_rgba(45,41,40,0.12)] transition-transform duration-200 lg:sticky lg:top-0 lg:w-64 lg:translate-x-0">

    <div>
        <div class="border-b border-white/10 pb-6">
            <div class="flex items-start justify-between">
                <h1 class="mb-1 font-['Cormorant_Garamond'] text-2xl font-semibold tracking-[0.06em] text-white">
                    MALU STORE
                </h1>

                <button type="button" @click="adminMenuOpen = false"
                    class="rounded-lg px-2 py-1 text-lg text-[#ded2cf] hover:bg-white/10 lg:hidden"
                    aria-label="Fechar menu">
                    ×
                </button>
            </div>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#e59aaa]">Painel Admin</p>
        </div>

        <nav>
            <p class="mb-3 mt-6 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-[#a99b98]">Gestão da loja</p>
            <ul class="space-y-1 text-sm font-semibold">
                <li>
                    <a @click="adminMenuOpen = false" href="{{ route('admin.dashboard') }}"
                        class="block rounded-xl px-3 py-2.5 transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#cf7184] text-white shadow-[0_6px_16px_rgba(207,113,132,0.25)]' : 'text-[#ded2cf] hover:bg-white/10 hover:text-white' }}">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a @click="adminMenuOpen = false" href="{{ route('admin.clients.index') }}"
                        class="block rounded-xl px-3 py-2.5 text-[#ded2cf] transition hover:bg-white/10 hover:text-white">
                        Clientes
                    </a>
                </li>

                <li>
                    <a @click="adminMenuOpen = false" href="{{ route('admin.admins.index') }}"
                        class="block rounded-xl px-3 py-2.5 text-[#ded2cf] transition hover:bg-white/10 hover:text-white">
                        Admins
                    </a>
                </li>

                <li>
                    <a @click="adminMenuOpen = false" href="{{ route('admin.categories.index') }}"
                        class="block rounded-xl px-3 py-2.5 text-[#ded2cf] transition hover:bg-white/10 hover:text-white">
                        Categorias
                    </a>
                </li>

                <li>
                    <a @click="adminMenuOpen = false" href="{{ route('admin.products.index') }}"
                        class="block rounded-xl px-3 py-2.5 transition {{ request()->routeIs('admin.products.*') ? 'bg-[#cf7184] text-white shadow-[0_6px_16px_rgba(207,113,132,0.25)]' : 'text-[#ded2cf] hover:bg-white/10 hover:text-white' }}">
                        Produtos
                    </a>
                </li>

                <li>
                    <a @click="adminMenuOpen = false" href="{{ route('admin.orders.index') }}"
                        class="block rounded-xl px-3 py-2.5 transition {{ request()->routeIs('admin.orders.*') ? 'bg-[#cf7184] text-white shadow-[0_6px_16px_rgba(207,113,132,0.25)]' : 'text-[#ded2cf] hover:bg-white/10 hover:text-white' }}">
                        Pedidos
                    </a>
                </li>

                <li>
                    <a @click="adminMenuOpen = false" href="{{ route('admin.shipments.index') }}"
                        class="block rounded-xl px-3 py-2.5 text-[#ded2cf] transition hover:bg-white/10 hover:text-white">
                        Envios
                    </a>
                </li>

            </ul>
        </nav>
    </div>

    <div class="border-t border-white/10 pt-4">

        <p class="mb-2 text-sm text-[#b8aaa7]">
            Logado como:
            <span class="font-semibold text-white">
                {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
            </span>
        </p>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf

            <button type="submit"
                class="w-full rounded-xl border border-[#d97789] bg-[#cf7184] py-2 text-sm font-semibold text-white transition hover:bg-[#b85d70]">
                Sair
            </button>
        </form>

    </div>

</aside>
