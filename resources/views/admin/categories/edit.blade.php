@extends('layouts.admin.app')

@section('title', 'Editar Categoria')

@section('content')

    <div class="mx-auto max-w-2xl">
        <div class="w-full">
            <a href="{{ route('admin.categories.index') }}"
                class="mb-4 inline-block text-sm font-semibold text-[#b85d70] hover:text-[#9f4c5e]">
                ← Voltar para categorias
            </a>

            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Catálogo</p>
            <h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold mb-6">Editar categoria</h1>

            <div class="max-w-xl rounded-2xl border border-[#eaded9] bg-white p-6 shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-4">
                    @method('PUT')
                    @include('admin.categories.form')

                    <div class="flex justify-between items-center pt-2">
                        <a href="{{ route('admin.categories.index') }}"
                            class="text-sm font-semibold text-[#746b68] hover:text-[#443d3b]">
                            Cancelar
                        </a>

                        <button class="rounded-xl bg-[#cf7184] px-4 py-3 text-sm font-bold text-white hover:bg-[#b85d70]">
                            Atualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
