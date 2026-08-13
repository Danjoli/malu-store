<div class="relative" x-data="{ open: false }">

    {{-- BOTÃO PERFIL --}}
    <button
        @click="open = !open"
        class="flex h-8 w-8 items-center justify-center rounded-full transition hover:bg-[#fff1ef]"
    >

        <svg xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-stone-700"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
            viewBox="0 0 24 24">
            <path d="M20 21a8 8 0 0 0-16 0M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/>
        </svg>

    </button>

    {{-- DROPDOWN --}}
    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        x-transition
        class="absolute right-0 mt-3 w-52 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
    >

        {{-- SE ESTIVER LOGADO --}}
        @auth

        <a href="{{ route('profile.edit') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Editar conta
        </a>

        <a href="{{ route('profile.orders') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Ver pedidos
        </a>

        <div class="border-t"></div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                Sair
            </button>
        </form>

        @endauth


        {{-- SE NÃO ESTIVER LOGADO --}}
        @guest

        <a href="{{ route('login') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Entrar
        </a>

        <a href="{{ route('register') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            Criar conta
        </a>

        @endguest

    </div>

</div>
