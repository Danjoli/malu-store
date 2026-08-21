@extends('layouts.public.app')

@section('title', 'Meu Perfil')

@section('content')
    <div class="store-container py-10 md:py-14">
        <div class="mb-8 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Minha conta</p>
                <h1 class="store-title mt-2 text-4xl md:text-5xl">Olá, {{ explode(' ', $user->name)[0] }}</h1>
                <p class="mt-2 text-sm text-[#746b68]">Atualize seus dados, endereços e preferências de acesso.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('profile.orders') }}" class="store-button store-button-outline">Meus pedidos</a>
                <a href="{{ route('favorites.index') }}" class="store-button store-button-outline">Favoritos</a>
            </div>
        </div>

        <div class="mb-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-md border border-[#eee6e4] bg-[#fff8f7] p-4"><p class="text-xs font-bold uppercase tracking-wide text-[#857b78]">Pedidos</p><p class="mt-1 text-2xl font-bold text-[#2d2928]">{{ $user->orders_count }}</p></div>
            <div class="rounded-md border border-[#eee6e4] bg-[#fff8f7] p-4"><p class="text-xs font-bold uppercase tracking-wide text-[#857b78]">Favoritos</p><p class="mt-1 text-2xl font-bold text-[#2d2928]">{{ $user->favorites_count }}</p></div>
            <div class="rounded-md border border-[#eee6e4] bg-[#fff8f7] p-4"><p class="text-xs font-bold uppercase tracking-wide text-[#857b78]">Endereços</p><p class="mt-1 text-2xl font-bold text-[#2d2928]">{{ $addresses->count() }}</p></div>
        </div>

        <div class="grid gap-8 md:grid-cols-2">
            {{-- Conta --}}
            <div class="rounded-md border border-[#eee6e4] bg-white p-6 shadow-[0_10px_30px_rgba(63,38,35,.05)] md:p-7">
                <x-public.profile.account-form :user="$user" />

                <div class="mt-10">
                    <x-public.profile.password-form />
                </div>
            </div>

            {{-- Endereços --}}
            <div class="rounded-md border border-[#eee6e4] bg-white p-6 shadow-[0_10px_30px_rgba(63,38,35,.05)] md:p-7">
                <x-public.profile.address-list :addresses="$addresses" />

                <div class="mt-6">
                <x-public.profile.address-form :can-add-address="$addresses->count() < 10" />
                </div>
            </div>
        </div>
    </div>
@endsection
