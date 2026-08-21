<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') | Malu Store Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#f8f3f1] font-sans text-[#2d2928] antialiased">

    <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">

        <section
            class="w-full max-w-md overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_12px_34px_rgba(76,50,47,0.08)]">

            {{-- Cabeçalho --}}
            <div class="border-b border-[#f0e5e1] bg-[#fdf8f6] px-7 py-8 text-center sm:px-9">

                <a href="{{ route('home') }}"
                    class="font-['Cormorant_Garamond'] text-3xl font-semibold tracking-[0.08em] text-[#2d2928] transition hover:text-[#b85d70]">
                    MALU STORE
                </a>

                <p class="mt-3 text-[10px] font-bold uppercase tracking-[0.22em] text-[#c96f82]">
                    Administração
                </p>

                <h1 class="mt-4 font-['Cormorant_Garamond'] text-3xl font-semibold text-[#2d2928]">
                    Painel Administrativo
                </h1>

                <p class="mt-2 text-sm text-[#746b68]">
                    Entre com suas credenciais para gerenciar a loja.
                </p>

            </div>

            {{-- Formulário --}}
            <div class="px-7 py-8 sm:px-9">

                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">

                    @csrf

                    {{-- Erros gerais --}}
                    @if ($errors->any())
                        <div
                            class="rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] px-4 py-3 text-sm text-[#b44259]"
                            role="alert">

                            <p class="font-bold">
                                Não foi possível entrar
                            </p>

                            <p class="mt-1 text-xs leading-5">
                                Verifique suas credenciais e tente novamente.
                            </p>

                        </div>
                    @endif

                    {{-- E-mail --}}
                    <div>

                        <label for="email" class="mb-2 block text-sm font-semibold text-[#443d3b]">
                            E-mail
                        </label>

                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            autocomplete="email" required autofocus placeholder="admin@malustore.com"
                            class="w-full rounded-xl border px-4 py-3 text-sm text-[#2d2928] outline-none transition placeholder:text-[#afa5a1]
                            {{ $errors->has('email')
                                ? 'border-[#e5a0ad] bg-[#fffafa] focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]'
                                : 'border-[#ded4d0] bg-white focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]' }}">

                        @error('email')
                            <p class="mt-2 text-xs font-medium text-[#b44259]">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    {{-- Senha --}}
                    <div>

                        <label for="password" class="mb-2 block text-sm font-semibold text-[#443d3b]">
                            Senha
                        </label>

                        <input id="password" type="password" name="password" autocomplete="current-password" required
                            placeholder="Digite sua senha"
                            class="w-full rounded-xl border border-[#ded4d0] bg-white px-4 py-3 text-sm text-[#2d2928] outline-none transition placeholder:text-[#afa5a1] focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">

                    </div>

                    <div class="-mt-2 text-right">
                        <a href="{{ route('admin.password.request') }}" class="text-xs font-bold text-[#b85d70] hover:text-[#9f4c5e]">
                            Esqueci minha senha
                        </a>
                    </div>

                    {{-- Botão --}}
                    <button type="submit"
                        class="w-full rounded-xl bg-[#cf7184] px-4 py-3.5 text-sm font-bold text-white transition hover:bg-[#b85d70] focus:outline-none focus:ring-4 focus:ring-[#f7dce2]">
                        Entrar no painel
                    </button>

                </form>

                {{-- Voltar --}}
                <div class="mt-6 text-center">

                    <a href="{{ route('home') }}"
                        class="text-xs font-semibold text-[#746b68] transition hover:text-[#b85d70]">
                        ← Voltar para a loja
                    </a>

                </div>

            </div>

            {{-- Rodapé --}}
            <footer class="border-t border-[#f0e5e1] bg-[#fffdfc] px-6 py-4 text-center">

                <p class="text-xs text-[#928784]">
                    © {{ now()->year }} Malu Store · Área restrita
                </p>

            </footer>

        </section>

    </main>

</body>

</html>
