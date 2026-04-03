<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>@yield('title') | Admin</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white p-5 flex flex-col justify-between h-screen">

            <div>
                <h1 class="text-2xl font-bold mb-6">Admin Panel</h1>

                <nav>
                    <ul>
                        <li class="mb-3"><a href="/admin" class="hover:text-gray-300">Dashboard</a></li>
                        <li class="mb-3"><a href="{{ route('admin.clients.index') }}" class="hover:text-gray-300">Clientes</a></li>
                        <li class="mb-3"><a href="{{ route('admin.admins.index') }}" class="hover:text-gray-300">Admins</a></li>
                        <li class="mb-3"><a href="{{ route('admin.categories.index') }}" class="hover:text-gray-300">Categorias</a></li>
                        <li class="mb-3"><a href="{{ route('admin.products.index') }}" class="hover:text-gray-300">Produtos</a></li>
                        <li class="mb-3"><a href="{{ route('admin.orders.index') }}" class="hover:text-gray-300">Pedidos</a></li>
                        <li class="mb-3"><a href="{{ route('admin.shipments.index') }}" class="hover:text-gray-300">Envios</a></li>
                    </ul>
                </nav>
            </div>

            <!-- Parte inferior -->
            <div class="border-t border-gray-700 pt-4">

                <p class="text-sm text-gray-400 mb-2">
                    Logado como:
                    <span class="font-semibold text-white">
                        {{ Auth::guard('admin')->user()->name ?? 'Admin' }}
                    </span>
                </p>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded transition">
                        Sair
                    </button>
                </form>

            </div>

        </aside>

        <!-- Conteúdo -->
        <main class="flex-1 p-10 overflow-y-auto h-screen">

            {{-- MENSAGENS --}}
            @include('includes.alerts')

            @yield('content')

        </main>

    </div>

@stack('scripts')

</body>
</html>
