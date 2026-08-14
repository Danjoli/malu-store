@extends('layouts.admin.app')

@section('title', 'Editar Administrador')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.admins.index') }}" class="text-sm font-semibold text-[#b85d70] hover:text-[#9f4c5e]">← Voltar
            para administradores</a>
        <p class="mt-6 text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Equipe</p>
        <h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold">Editar administrador</h1>
        <p class="mt-1 text-sm text-[#746b68]">Atualize os dados de {{ $admin->name }}.</p>
        <form action="{{ route('admin.admins.update', $admin) }}" method="POST"
            class="mt-7 max-w-xl space-y-5 rounded-2xl border border-[#eaded9] bg-white p-6 shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
            @csrf @method('PUT') @include('admin.admins.form')
            <div class="flex items-center justify-between border-t border-[#f0e5e1] pt-5"><a
                    href="{{ route('admin.admins.index') }}"
                    class="text-sm font-semibold text-[#746b68] hover:text-[#443d3b]">Cancelar</a><button
                    class="rounded-xl bg-[#cf7184] px-4 py-3 text-sm font-bold text-white hover:bg-[#b85d70]">Salvar
                    alterações</button></div>
        </form>
    </div>
@endsection
