@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')

<div class="container mx-auto max-w-6xl py-10">

    <h1 class="text-3xl font-bold text-pink-600 mb-10">
        Meu Perfil
    </h1>

    <div class="grid md:grid-cols-2 gap-10">

        <!-- CONTA -->
        <div class="bg-white p-6 rounded-xl shadow">

            <x-public::profile.account-form :user="$user" />

            <div class="mt-10">
                <x-public::profile.password-form />
            </div>

        </div>


        <!-- ENDEREÇOS -->
        <div class="bg-white p-6 rounded-xl shadow">

            <x-public::profile.address-list :addresses="$addresses" />

            <div class="mt-6">
                <x-public::profile.address-form />
            </div>

        </div>

    </div>

</div>

@endsection
