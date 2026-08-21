@extends('layouts.admin.app')

@section('title', 'Editar Produto')

@section('content')
    <h1 class="mb-4 text-2xl font-bold">
        Editar Produto
    </h1>

    <form
        action="{{ route('admin.products.update', $product) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        @include('admin.products.form', [
            'product' => $product,
            'button' => 'Atualizar Produto',
        ])
    </form>
@endsection

@push('scripts')
    <script>
        window.PRODUCT = {
            variantIndex: @json($variantIndex),
        };
        window.CLOTHING_SIZES = @json(array_map(fn ($size) => $size->value, $sizes));
    </script>
@endpush
