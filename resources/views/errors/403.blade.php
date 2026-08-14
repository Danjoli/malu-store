@extends('layouts.public.app')

@section('title', 'Acesso negado')

@section('content')
    <x-error-page code="403" eyebrow="Área restrita" title="Você não tem acesso a esta página"
        message="Sua conta não possui permissão para abrir este conteúdo. Se acredita que isso é um engano, entre em contato com a gente."
        :action-url="route('home')" />
@endsection
