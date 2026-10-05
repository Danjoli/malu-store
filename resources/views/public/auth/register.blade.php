@extends('layouts.public.app')

@section('title', 'Criar Conta')

@section('content')
    <div class="mx-auto flex min-h-[58vh] max-w-md items-center px-5 py-12">
        <section class="auth-card w-full">
            <div class="auth-card-header sm:px-8">
                <p class="store-kicker">
                    Minha conta
                </p>

                <h1 class="store-title mt-2 text-3xl font-bold text-[var(--store-text)] sm:text-4xl">
                    Crie sua conta
                </h1>

                <p class="store-muted mt-2 text-sm font-medium">
                    Cadastre-se para comprar e acompanhar seus pedidos.
                </p>
            </div>

            <form method="POST" action="/register" class="auth-card-body space-y-5 sm:px-8">
                @csrf

                @if ($errors->any())
                    <div
                        class="rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] px-4 py-3 text-sm text-[#b44259]"
                        role="alert"
                    >
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="name" class="auth-label">
                        Nome completo
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        placeholder="Seu nome"
                        class="store-input"
                        required
                        autofocus
                    >
                </div>

                <div>
                    <label for="email" class="auth-label">
                        E-mail
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        placeholder="voce@email.com"
                        class="store-input"
                        required
                    >
                </div>

                <div>
                    <label for="phone" class="auth-label">
                        Telefone
                    </label>

                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        autocomplete="tel"
                        inputmode="tel"
                        placeholder="(11) 99999-9999"
                        class="store-input"
                        required
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
                        autocomplete="new-password"
                        minlength="8"
                        placeholder="8+ caracteres, maiúscula, número e símbolo"
                        class="store-input"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="store-button store-button-primary w-full rounded-xl py-3.5"
                >
                    Criar conta
                </button>

                <p class="store-muted text-center text-sm font-medium">
                    Já tem uma conta?
                    <a
                        href="{{ route('login') }}"
                        class="auth-link"
                    >
                        Entrar
                    </a>
                </p>
            </form>
        </section>
    </div>
@endsection
