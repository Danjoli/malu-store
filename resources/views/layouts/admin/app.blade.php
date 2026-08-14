<!DOCTYPE html>
<html lang="pt-br">

@include('layouts.admin.partials.head')

<body x-data="{ adminMenuOpen: false, sidebarCollapsed: false }" class="bg-[#f8f3f1] font-sans text-[#2d2928]">

    <div class="flex min-h-screen">

        @include('layouts.admin.partials.sidebar')

        <div x-show="adminMenuOpen" x-transition.opacity @click="adminMenuOpen = false"
            class="fixed inset-0 z-40 bg-[#2d2928]/45 lg:hidden" x-cloak></div>

        <main class="min-w-0 flex-1 p-5 sm:p-8 lg:p-10">
            <div class="mb-6 flex items-center justify-between lg:hidden">
                <button type="button" @click="adminMenuOpen = true"
                    class="rounded-xl border border-[#ddc9c4] bg-white px-3 py-2 text-sm font-bold text-[#443d3b]">
                    ☰ Menu
                </button>
                <span class="font-['Cormorant_Garamond'] text-xl font-semibold tracking-[0.06em]">MALU STORE</span>
            </div>
            <button type="button" @click="sidebarCollapsed = !sidebarCollapsed"
                class="mb-6 hidden items-center gap-2 rounded-xl border border-[#ddc9c4] bg-white px-3 py-2 text-sm font-bold text-[#443d3b] transition hover:border-[#cf7184] hover:text-[#b85d70] lg:inline-flex"
                :aria-label="sidebarCollapsed ? 'Mostrar menu lateral' : 'Recolher menu lateral'">
                <span x-text="sidebarCollapsed ? '☰' : '‹'"></span>
                <span x-text="sidebarCollapsed ? 'Mostrar menu' : 'Recolher menu'"></span>
            </button>

            @include('components.ui.alerts')

            @yield('content')

        </main>

    </div>

    @include('layouts.admin.partials.scripts')

</body>

</html>
