@extends('layouts.admin.app')

@section('title', 'Editar Categoria')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a
            href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center gap-1 text-sm font-semibold text-[#b85d70] transition hover:text-[#9f4c5e]"
        >
            ← Voltar para categorias
        </a>

        <div class="mb-7 mt-6">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">
                Catálogo
            </p>

            <h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold text-[#2d2928]">
                Editar categoria
            </h1>

            <p class="mt-1 text-sm text-[#746b68]">
                Atualize as informações da categoria selecionada.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
            <div class="border-b border-[#f0e5e1] bg-[#fdf8f6] px-6 py-4">
                <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold text-[#2d2928]">
                    Informações da categoria
                </h2>

                <p class="mt-1 text-xs text-[#857b78]">
                    Altere os dados necessários e salve as mudanças.
                </p>
            </div>

            <form
                action="{{ route('admin.categories.update', $category) }}"
                method="POST"
                class="p-6"
            >
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    @include('admin.categories.form')
                </div>

                <div class="mt-7 flex items-center justify-between border-t border-[#f0e5e1] pt-5">
                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="text-sm font-semibold text-[#746b68] transition hover:text-[#443d3b]"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="rounded-xl bg-[#cf7184] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#b85d70]"
                    >
                        Atualizar categoria
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
