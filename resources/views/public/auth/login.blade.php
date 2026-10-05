@extends('layouts.public.app')

@section('title', 'Entrar')

@section('content')
    <div class="mx-auto flex min-h-[58vh] max-w-md items-center px-5 py-12">
        <section class="auth-card w-full">
            <div class="auth-card-header sm:px-8">
                <p class="store-kicker">
                    Minha conta
                </p>

                <h1 class="store-title mt-2 text-3xl font-bold text-[var(--store-text)] sm:text-4xl">
                    Que bom te ver
                </h1>

                <p class="store-muted mt-2 text-sm font-medium">
                    Entre para acompanhar seus pedidos e favoritos.
                </p>
            </div>

            <form method="POST" action="/login" class="auth-card-body space-y-5 sm:px-8">
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
                    <label for="email" class="auth-label">
                        E-mail ou telefone
                    </label>

                    <input
                        id="email"
                        type="text"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        placeholder="voce@email.com ou seu telefone"
                        class="store-input"
                        required
                        autofocus
                    >
                </div>

                <div>
                    <label for="password" class="auth-label">
                        Senha
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        placeholder="Sua senha"
                        class="store-input"
                        required
                    >
                </div>

                <div class="-mt-2 text-right">
                    <a href="{{ route('password.request') }}" class="auth-link text-xs">
                        Esqueci minha senha
                    </a>
                </div>

                <button class="store-button store-button-primary w-full rounded-xl py-3.5">
                    Entrar
                </button>

                <p class="store-muted text-center text-sm font-medium">
                    Ainda não tem conta?
                    <a
                        href="{{ route('register') }}"
                        class="auth-link"
                    >
                        Criar conta
                    </a>
                </p>
            </form>
        </section>
    </div>
@endsection
