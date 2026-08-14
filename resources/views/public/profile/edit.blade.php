@extends('layouts.public.app')

@section('title', 'Meu Perfil')

@section('content')

<div class="store-container py-10 md:py-14">

    <h1 class="store-title mb-8 text-4xl md:text-5xl">
        Meu Perfil
    </h1>

    <div class="grid gap-8 md:grid-cols-2">

        <!-- CONTA -->
        <div class="rounded-md border border-[#eee6e4] bg-white p-6 md:p-7">

            <x-public.profile.account-form :user="$user" />

            <div class="mt-10">
                <x-public.profile.password-form />
            </div>

        </div>


        <!-- ENDEREÇOS -->
        <div class="rounded-md border border-[#eee6e4] bg-white p-6 md:p-7">

            <x-public.profile.address-list :addresses="$addresses" />

            <div class="mt-6">
                <x-public.profile.address-form />
            </div>

        </div>

    </div>

</div>

@endsection
