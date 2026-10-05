@extends('layouts.public.app')

@section('title', 'Nova senha')

@section('content')
    <div class="mx-auto flex min-h-[58vh] max-w-md items-center px-5 py-12">
        <section class="auth-card w-full">
            <div class="auth-card-header sm:px-8">
                <p class="store-kicker">Minha conta</p>
                <h1 class="store-title mt-2 text-3xl font-bold text-[var(--store-text)] sm:text-4xl">Crie uma nova senha</h1>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="auth-card-body space-y-4 sm:px-8">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="auth-label">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" class="store-input">
                </div>
                <div>
                    <label for="password" class="auth-label">Nova senha</label>
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" placeholder="Nova senha" class="store-input">
                </div>
                <div>
                    <label for="password_confirmation" class="auth-label">Confirme a nova senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" placeholder="Confirme a nova senha" class="store-input">
                </div>

                @error('email')
                    <p class="text-xs font-semibold text-[var(--store-accent)]">{{ $message }}</p>
                @enderror

                <button class="store-button store-button-primary w-full rounded-xl py-3.5">Atualizar senha</button>
            </form>
        </section>
    </div>
@endsection
