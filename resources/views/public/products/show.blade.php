@extends('layouts.public.app')

@section('title', $product->name)

@section('content')
@php
    $availableVariants = $product->variants->where('stock', '>', 0);
    $stock = $availableVariants->sum('stock');
    $colors = $availableVariants->pluck('color')->filter()->unique();
    $sizes = $availableVariants->pluck('size')->filter()->unique();
@endphp
<div class="store-container py-7 md:py-10">
    <div class="mb-6 text-xs text-stone-500">Home <span class="mx-1">›</span> {{ $product->category?->name }} <span class="mx-1">›</span> {{ $product->name }}</div>
    <div class="grid gap-9 lg:grid-cols-[1.05fr_.95fr] lg:gap-12">
        <div class="flex gap-3">
            <div class="hidden w-16 shrink-0 space-y-2 sm:block">@foreach($product->images as $image)<button type="button" class="block h-20 w-16 overflow-hidden rounded-sm border border-[#eadfdd]" onclick="document.getElementById('mainImage').src=this.querySelector('img').src"><img src="{{ asset('storage/products/'.$image->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover"></button>@endforeach</div>
            <div class="min-w-0 flex-1 bg-[#f7efe9]">@if($product->images->isNotEmpty())<img id="mainImage" src="{{ asset('storage/products/'.$product->images->first()->image) }}" alt="{{ $product->name }}" class="aspect-[3/4] w-full object-cover">@endif</div>
        </div>
        <div>
            <h1 class="store-title text-4xl leading-none text-stone-900">{{ $product->name }}</h1>
            <p class="mt-3 text-sm tracking-wide text-amber-500">★★★★★ <span class="ml-1 text-xs text-stone-500">(48 avaliações)</span></p>
            <p class="mt-5 text-3xl font-bold text-stone-900">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
            <p class="mt-1 text-xs text-stone-600">3x de R$ {{ number_format($product->price / 3, 2, ',', '.') }} sem juros</p>
            <p class="mt-6 max-w-md text-sm leading-6 text-stone-600">{{ $product->description }}</p>
            <form action="{{ route('cart.add') }}" method="POST" class="mt-6">@csrf
                <p class="mb-2 text-xs font-bold text-stone-700">Cor: <span class="font-normal">{{ $colors->first() ?? 'Única' }}</span></p>
                <div class="mb-5 flex gap-2">@foreach($colors as $color)<span title="{{ $color }}" class="h-5 w-5 rounded-full border border-stone-300 bg-[#e9d5cc]"></span>@endforeach</div>
                @if($sizes->isNotEmpty())<p class="mb-2 text-xs font-bold text-stone-700">Tamanho:</p><div class="mb-3 flex flex-wrap gap-2">@foreach($sizes as $size)<label><input class="peer sr-only" type="radio" name="variant_id" value="{{ $availableVariants->firstWhere('size', $size)->id }}" @checked($loop->first) required><span class="flex h-8 min-w-9 items-center justify-center rounded-sm border border-[#eadfdd] px-2 text-xs peer-checked:border-[#d66f7c] peer-checked:bg-[#fff1ef]">{{ $size }}</span></label>@endforeach</div>@endif
                <a href="#descricao" class="text-xs font-semibold underline underline-offset-4">Guia de medidas</a>
                <input type="hidden" name="quantity" value="1">
                @if($stock > 0)<button class="store-button store-button-primary mt-5 w-full">♧ Adicionar à sacola</button>@else<button disabled class="mt-5 w-full rounded bg-stone-300 py-3 text-xs text-white">Produto esgotado</button>@endif
                <button type="button" class="store-button store-button-outline mt-2 w-full">♡ Adicionar aos favoritos</button>
            </form>
        </div>
    </div>
    <section class="mt-8 max-w-xl rounded-md border border-[#eee6e4] bg-white p-5"><p class="mb-3 text-xs font-bold">Calcule o frete</p><div class="flex gap-2"><input class="store-input py-2 text-xs" placeholder="Digite seu CEP"><button class="store-button store-button-outline py-2 text-[10px]">Calcular</button></div></section>
    <section class="mt-6 grid gap-4 rounded-md border border-[#eee6e4] bg-white p-5 text-center text-[10px] sm:grid-cols-4"><div>▱<p class="mt-1 font-bold">Envio rápido</p><span class="text-stone-500">para todo o Brasil</span></div><div>↺<p class="mt-1 font-bold">Troca fácil</p><span class="text-stone-500">até 7 dias</span></div><div>♢<p class="mt-1 font-bold">Compra segura</p><span class="text-stone-500">dados protegidos</span></div><div>▤<p class="mt-1 font-bold">Parcele em até 6x</p><span class="text-stone-500">sem juros</span></div></section>
    <section id="descricao" class="mt-8 border-t border-[#eee6e4] pt-5"><div class="flex gap-6 border-b border-[#eee6e4] text-[10px] font-bold uppercase tracking-wide text-stone-700"><span class="border-b-2 border-[#d66f7c] pb-3">Descrição</span><span class="pb-3 text-stone-400">Detalhes</span><span class="pb-3 text-stone-400">Composição</span><span class="pb-3 text-stone-400">Avaliações (48)</span></div><p class="max-w-2xl py-5 text-xs leading-6 text-stone-600">{{ $product->description }}</p></section>
    @if($relatedProducts->isNotEmpty())<section class="mt-8"><h2 class="mb-5 text-center text-xs font-bold uppercase tracking-wide">Você também pode gostar</h2><div class="grid grid-cols-2 gap-4 sm:grid-cols-4">@foreach($relatedProducts as $relatedProduct)<x-store.product-card :product="$relatedProduct" />@endforeach</div></section>@endif
</div>
@endsection
