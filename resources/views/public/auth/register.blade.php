@extends('layouts.public.app')

@section('title', 'Criar Conta')

@section('content')
    <div class="mx-auto flex min-h-[58vh] max-w-md items-center px-5 py-12">
        <section
            class="w-full overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_12px_34px_rgba(76,50,47,0.08)]">

            <div class="border-b border-[#f0e5e1] bg-[#fdf8f6] px-6 py-7 text-center sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">
                    Minha conta
                </p>
                <h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold text-[#2d2928]">
                    Crie sua conta
                </h1>
                <p class="mt-2 text-sm text-[#746b68]">
                    Cadastre-se para comprar e acompanhar seus pedidos.
                </p>
            </div>

            <form method="POST" action="/register" class="space-y-5 px-6 py-7 sm:px-8">
                @csrf

                @if ($errors->any())
                    <div class="rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] px-4 py-3 text-sm text-[#b44259]" role="alert">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-[#443d3b]">Nome completo</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name"
                        required autofocus placeholder="Seu nome"
                        class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none transition focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-[#443d3b]">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email"
                        required placeholder="voce@email.com"
                        class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none transition focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-semibold text-[#443d3b]">Telefone</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                        inputmode="tel" required placeholder="(11) 99999-9999"
                        class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none transition focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-[#443d3b]">Senha</label>
                    <input id="password" type="password" name="password" autocomplete="new-password" minlength="6"
                        required placeholder="Mínimo de 6 caracteres"
                        class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none transition focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-[#cf7184] py-3.5 text-sm font-bold text-white transition hover:bg-[#b85d70]">Criar conta</button>

                <p class="text-center text-sm text-[#746b68]">Já tem uma conta?
                    <a href="{{ route('login') }}"
                        class="font-bold text-[#b85d70] hover:text-[#9f4c5e]">Entrar
                    </a>
                </p>
            </form>
        </section>
    </div>
@endsection
