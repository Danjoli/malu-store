@extends('layouts.public.app')

@section('title', 'Página não encontrada')

@section('content')
    <x-error-page
        code="404"
        eyebrow="Página não encontrada"
        title="Essa página não está por aqui"
        message="Ela pode ter mudado de endereço, sido removida ou o link pode estar incompleto."
        :action-url="route('home')"
    />
@endsection
