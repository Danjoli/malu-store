<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Admin</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="flex w-full max-w-2xl bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="w-full p-10">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Painel Administrativo</h2>
            <p class="text-center text-gray-500 mb-8">Faça login para acessar o dashboard</p>

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Senha -->
                <div>
                    <label class="block text-gray-700 mb-2">Senha</label>
                    <input type="password" name="password"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white p-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Entrar
                </button>
            </form>

            <p class="mt-6 text-center text-gray-500 text-sm">
                &copy; 2026 Sua Empresa. Todos os direitos reservados.
            </p>
        </div>
    </div>

</body>
</html>
