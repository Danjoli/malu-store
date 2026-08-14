@extends('layouts.public.app')

@section('title', 'Entrar')

@section('content')
    <div class="mx-auto flex min-h-[58vh] max-w-md items-center px-5 py-12">
        <section
            class="w-full overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_12px_34px_rgba(76,50,47,0.08)]">
            <div class="border-b border-[#f0e5e1] bg-[#fdf8f6] px-6 py-7 text-center sm:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Minha conta</p>
                <h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold text-[#2d2928]">Que bom te ver</h1>
                <p class="mt-2 text-sm text-[#746b68]">Entre para acompanhar seus pedidos e favoritos.</p>
            </div>

            <form method="POST" action="/login" class="space-y-5 px-6 py-7 sm:px-8">
                @csrf

                @if ($errors->any())
                    <div class="rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] px-4 py-3 text-sm text-[#b44259]">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-[#443d3b]">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email"
                        required autofocus placeholder="voce@email.com"
                        class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none transition focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-[#443d3b]">Senha</label>
                    <input id="password" type="password" name="password" autocomplete="current-password" required
                        placeholder="Sua senha"
                        class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none transition focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </div>

                <button
                    class="w-full rounded-xl bg-[#cf7184] py-3.5 text-sm font-bold text-white transition hover:bg-[#b85d70]">Entrar</button>

                <p class="text-center text-sm text-[#746b68]">Ainda não tem conta? <a href="{{ route('register') }}"
                        class="font-bold text-[#b85d70] hover:text-[#9f4c5e]">Criar conta</a></p>
            </form>
        </section>
    </div>
@endsection
