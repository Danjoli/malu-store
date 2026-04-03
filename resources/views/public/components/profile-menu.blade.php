<div class="relative" x-data="{ open: false }">

    {{-- BOTÃO PERFIL --}}
    <button
        @click="open = !open"
        class="flex items-center justify-center w-9 h-9 rounded-full hover:bg-gray-100 transition"
    >

        <svg xmlns="http://www.w3.org/2000/svg"
            class="w-7 h-7 text-gray-700"
            fill="currentColor"
            viewBox="0 0 24 24">
            <path fill-rule="evenodd"
                d="M18 20a6 6 0 10-12 0h12zm-6-8a4 4 0 100-8 4 4 0 000 8z"
                clip-rule="evenodd"/>
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
