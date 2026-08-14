@extends('layouts.admin.app')

@section('title', 'Categorias')

@section('content')

    <x-admin.page-header
        eyebrow="Catálogo"
        title="Categorias"
        description="Organize e gerencie as categorias dos produtos da sua loja."
    >
        <x-slot:actions>
            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-[#cf7184] px-4 py-3 text-sm font-bold text-white transition hover:bg-[#b85d70]"
            >
                + Nova categoria
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.table-card>

        <table class="w-full min-w-[620px] text-left text-sm">

            {{-- Cabeçalho --}}
            <thead class="bg-[#fdf8f6] text-xs font-bold uppercase tracking-wide text-[#746b68]">
                <tr>
                    <th class="px-5 py-4">
                        Categoria
                    </th>

                    <th class="px-5 py-4">
                        Slug
                    </th>

                    <th class="px-5 py-4 text-right">
                        Ações
                    </th>
                </tr>
            </thead>

            {{-- Conteúdo --}}
            <tbody class="divide-y divide-[#f0e5e1]">

                @forelse ($categories as $category)

                    <tr class="transition hover:bg-[#fdf8f6]">

                        {{-- Categoria --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#fdf0f3] font-['Cormorant_Garamond'] text-lg font-bold text-[#b85d70]"
                                >
                                    {{ mb_strtoupper(mb_substr($category->name, 0, 1)) }}
                                </div>

                                <div>
                                    <p class="font-bold text-[#3e3532]">
                                        {{ $category->name }}
                                    </p>

                                    @if (isset($category->products_count))
                                        <p class="mt-0.5 text-xs text-[#857b78]">
                                            {{ $category->products_count }}
                                            {{ $category->products_count === 1 ? 'produto' : 'produtos' }}
                                        </p>
                                    @endif
                                </div>

                            </div>
                        </td>

                        {{-- Slug --}}
                        <td class="px-5 py-4">

                            <span
                                class="inline-flex rounded-lg bg-[#f8f3f1] px-2.5 py-1 font-mono text-xs text-[#625956]"
                            >
                                {{ $category->slug }}
                            </span>

                        </td>

                        {{-- Ações --}}
                        <td class="px-5 py-4 text-right">

                            <div class="flex items-center justify-end gap-3">

                                <a
                                    href="{{ route('admin.categories.show', $category) }}"
                                    class="text-xs font-bold text-[#625956] transition hover:text-[#b85d70]"
                                >
                                    Ver
                                </a>

                                <a
                                    href="{{ route('admin.categories.edit', $category) }}"
                                    class="text-xs font-bold text-[#b85d70] transition hover:text-[#9f4c5e]"
                                >
                                    Editar
                                </a>

                                <form
                                    action="{{ route('admin.categories.destroy', $category) }}"
                                    method="POST"
                                    class="inline"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-xs font-bold text-[#b44259] transition hover:text-[#923548]"
                                        onclick="return confirm('Tem certeza que deseja excluir esta categoria?')"
                                    >
                                        Excluir
                                    </button>
                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="3" class="px-5 py-16 text-center">

                            <div class="mx-auto max-w-sm">

                                <div
                                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#fdf0f3] text-xl text-[#b85d70]"
                                >
                                    +
                                </div>

                                <p class="mt-4 font-semibold text-[#443d3b]">
                                    Nenhuma categoria cadastrada
                                </p>

                                <p class="mt-1 text-sm text-[#857b78]">
                                    Crie sua primeira categoria para começar a organizar os produtos.
                                </p>

                                <a
                                    href="{{ route('admin.categories.create') }}"
                                    class="mt-5 inline-flex rounded-xl bg-[#cf7184] px-4 py-2.5 text-sm font-bold text-white transition hover:bg-[#b85d70]"
                                >
                                    Criar categoria
                                </a>

                            </div>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </x-admin.table-card>

@endsection
