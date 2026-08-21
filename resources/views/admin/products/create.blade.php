@extends('layouts.admin.app')

@section('title', 'Criar Produto')

@section('content')
    <h1 class="mb-4 text-2xl font-bold">
        Novo Produto
    </h1>

    <form
        action="{{ route('admin.products.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf

        @include('admin.products.form', [
            'product' => null,
            'button' => 'Criar Produto',
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
