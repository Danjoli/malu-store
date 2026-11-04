@extends('layouts.admin.app')

@section('title', 'Produtos')

@push('head')
    @inertiaHead
@endpush

@section('content')
    @inertia
@endsection

@push('scripts')
    @vite('resources/js/inertia.js')
@endpush
