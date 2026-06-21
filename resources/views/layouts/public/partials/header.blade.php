<header class="bg-white shadow-sm">
    <div class="container mx-auto px-6 py-4 flex items-center justify-between">

        <a href="/" class="text-2xl font-bold text-blue-600 tracking-tight">
            Malu Store
        </a>

        <nav class="flex items-center gap-6 text-sm font-medium">

            <a href="/" class="text-gray-700 hover:text-blue-600 transition">
                Loja
            </a>

            <a href="{{ route('public.cart.index') }}" class="text-gray-700 hover:text-blue-600 transition">
                Carrinho
            </a>

            @include('components.public.profile-menu')

        </nav>

    </div>
</header>
