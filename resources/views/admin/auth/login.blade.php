<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Admin</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-[#f8f3f1] text-[#2d2928]">
    <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
        <section
            class="w-full max-w-md overflow-hidden rounded-[28px] border border-[#eaded9] bg-white shadow-[0_20px_60px_rgba(76,50,47,0.10)]">
            <div class="border-b border-[#f0e5e1] bg-[#fdf8f6] px-7 py-8 text-center sm:px-10">
                <a href="{{ route('home') }}"
                    class="font-['Cormorant_Garamond'] text-3xl tracking-[0.08em] text-[#2d2928]">MALU STORE</a>
                <p class="mt-3 text-[10px] font-bold uppercase tracking-[0.22em] text-[#c96f82]">Administração</p>
                <h1 class="mt-4 font-['Cormorant_Garamond'] text-3xl font-semibold text-[#2d2928]">Painel Administrativo
                </h1>
                <p class="mt-2 text-sm text-[#746b68]">Entre para gerenciar a sua loja.</p>
            </div>

            <div class="px-7 py-8 sm:px-10">
                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-[#443d3b]">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            autocomplete="email" required autofocus
                            class="w-full rounded-xl border border-[#ded4d0] bg-white px-4 py-3 text-sm text-[#2d2928] outline-none transition placeholder:text-[#afa5a1] focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">

                        @error('email')
                            <p class="mt-2 text-sm text-[#bd445d]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-[#443d3b]">Senha</label>
                        <input id="password" type="password" name="password" autocomplete="current-password" required
                            class="w-full rounded-xl border border-[#ded4d0] bg-white px-4 py-3 text-sm text-[#2d2928] outline-none transition placeholder:text-[#afa5a1] focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                    </div>

                    <button type="submit"
                        class="w-full rounded-xl bg-[#cf7184] px-4 py-3.5 text-sm font-bold text-white transition hover:bg-[#b85d70] focus:outline-none focus:ring-4 focus:ring-[#f7dce2]">
                        Entrar no painel
                    </button>
                </form>
            </div>

            <footer class="border-t border-[#f0e5e1] px-6 py-4 text-center text-xs text-[#928784]">
                &copy; {{ now()->year }} Malu Store. Área restrita.
            </footer>
        </section>
    </main>
</body>

</html>
