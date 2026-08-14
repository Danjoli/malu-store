@extends('layouts.public.app')

@section('title', 'Erro interno')

@section('content')
    <x-error-page
        code="500"
        eyebrow="Erro temporário"
        title="Não foi possível concluir esta ação"
        message="Nossa equipe foi avisada. Tente novamente em alguns instantes ou volte para a loja."
        :action-url="route('home')"
    />
@endsection
