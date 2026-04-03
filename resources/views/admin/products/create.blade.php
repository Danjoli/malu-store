@extends('layouts.admin')

@section('title', 'Criar Produto')

@section('content')
<h1 class="text-2xl font-bold mb-4">Novo Produto</h1>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @include('admin.products.form', [
        'product' => null,
        'button' => 'Criar Produto'
    ])
</form>
@endsection
