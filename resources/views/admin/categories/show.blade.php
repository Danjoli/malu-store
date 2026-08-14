@extends('layouts.admin.app')

@section('title', 'Detalhes da Categoria')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-[#b85d70] hover:text-[#9f4c5e]">← Voltar para categorias</a>
        <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Catálogo</p><h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold">{{ $category->name }}</h1><p class="mt-1 text-sm text-[#746b68]">Detalhes da categoria.</p></div><a href="{{ route('admin.categories.edit', $category) }}" class="rounded-xl bg-[#cf7184] px-4 py-3 text-center text-sm font-bold text-white hover:bg-[#b85d70]">Editar categoria</a></div>
        <section class="mt-7 overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_8px_24px_rgba(76,50,47,0.05)]"><dl class="divide-y divide-[#f0e5e1]"><div class="grid gap-1 p-5 sm:grid-cols-3"><dt class="text-sm font-semibold text-[#746b68]">Identificador</dt><dd class="sm:col-span-2 font-bold text-[#3e3532]">#{{ $category->id }}</dd></div><div class="grid gap-1 p-5 sm:grid-cols-3"><dt class="text-sm font-semibold text-[#746b68]">Nome</dt><dd class="sm:col-span-2 font-bold text-[#3e3532]">{{ $category->name }}</dd></div><div class="grid gap-1 p-5 sm:grid-cols-3"><dt class="text-sm font-semibold text-[#746b68]">Slug</dt><dd class="sm:col-span-2 font-mono text-sm text-[#625956]">{{ $category->slug }}</dd></div><div class="grid gap-5 p-5 sm:grid-cols-2"><div><dt class="text-sm font-semibold text-[#746b68]">Criada em</dt><dd class="mt-1 text-[#3e3532]">{{ $category->created_at->format('d/m/Y H:i') }}</dd></div><div><dt class="text-sm font-semibold text-[#746b68]">Última atualização</dt><dd class="mt-1 text-[#3e3532]">{{ $category->updated_at->format('d/m/Y H:i') }}</dd></div></div></dl></section>
    </div>
@endsection
