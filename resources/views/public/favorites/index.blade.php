@extends('layouts.public.app')

@section('title', 'Favoritos')

@section('content')
    <div class="store-container py-10 md:py-14">
        <div class="mb-8 flex items-center gap-4"><span class="h-px flex-1 bg-[#eadfdd]"></span>
            <h1 class="store-title text-4xl">Meus favoritos</h1><span class="h-px flex-1 bg-[#eadfdd]"></span>
        </div>
        @if ($products->isNotEmpty())
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 lg:gap-6">
                @foreach ($products as $product)
                    <x-store.product-card :product="$product" />
                @endforeach
        </div>@else<div class="rounded-md border border-dashed border-[#e8d9d6] py-16 text-center">
                <p class="text-stone-500">Você ainda não favoritou nenhum produto.</p><a href="{{ route('catalog.index') }}"
                    class="store-button store-button-primary mt-5">Ver produtos</a>
            </div>
        @endif
    </div>
@endsection
