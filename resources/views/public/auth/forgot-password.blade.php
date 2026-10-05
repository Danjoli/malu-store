@extends('layouts.public.app')

@section('title', 'Recuperar senha')

@section('content')
    <div class="mx-auto flex min-h-[58vh] max-w-md items-center px-5 py-12">
        <section class="auth-card w-full">
            <div class="auth-card-header sm:px-8">
                <p class="store-kicker">Minha conta</p>
                <h1 class="store-title mt-2 text-3xl font-bold text-[var(--store-text)] sm:text-4xl">Recuperar senha</h1>
                <p class="store-muted mt-2 text-sm font-medium">Enviaremos um link seguro para seu e-mail.</p>
            </div>

            <div class="auth-card-body sm:px-8">
                @if (session('success'))
                    <p class="mb-5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-700 dark:text-emerald-300">{{ session('success') }}</p>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="auth-label">E-mail</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="voce@email.com" class="store-input">
                        @error('email')
                            <p class="mt-2 text-xs font-semibold text-[var(--store-accent)]">{{ $message }}</p>
                        @enderror
                    </div>
                    <button class="store-button store-button-primary w-full rounded-xl py-3.5">Enviar link</button>
                </form>

                <a href="{{ route('login') }}" class="auth-link mt-5 block text-center text-sm">Voltar para entrar</a>
            </div>
        </section>
    </div>
@endsection
