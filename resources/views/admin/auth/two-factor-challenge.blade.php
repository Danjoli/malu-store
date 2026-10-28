<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar acesso | Malu Store Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8f3f1] font-sans text-[#2d2928]">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md rounded-2xl border border-[#eaded9] bg-white p-7 shadow-[0_12px_34px_rgba(76,50,47,0.08)] sm:p-9">
            <p class="text-[10px] font-bold uppercase tracking-[.22em] text-[#c96f82]">Verificação em duas etapas</p>
            <h1 class="mt-3 font-['Cormorant_Garamond'] text-4xl font-semibold">Confirme seu acesso</h1>
            <p class="mt-2 text-sm leading-6 text-[#746b68]">Enviamos um código de seis dígitos ao e-mail administrativo. Ele expira em 10 minutos.</p>

            <form method="POST" action="{{ route('admin.two-factor.verify') }}" class="mt-6 space-y-4">
                @csrf
                <label for="code" class="block text-sm font-semibold">Código de segurança</label>
                <input id="code" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus
                    class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-center text-2xl tracking-[.35em] outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                @error('code')<p class="text-xs font-semibold text-[#b44259]">{{ $message }}</p>@enderror
                <button class="w-full rounded-xl bg-[#cf7184] py-3 text-sm font-bold text-white">Confirmar e entrar</button>
            </form>

            <a href="{{ route('admin.login') }}" class="mt-5 block text-center text-sm font-bold text-[#b85d70]">Cancelar e voltar</a>
        </section>
    </main>
</body>
</html>
