@extends('layouts.admin')

@section('title', 'Editar Produto')

@section('content')
<h1 class="text-2xl font-bold mb-4">Editar Produto</h1>

<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.products.form', [
        'product' => $product,
        'button' => 'Atualizar Produto'
    ])
</form>
@endsection
