@extends('layouts.admin.app')

@section('title', 'Clientes')

@section('content')
    <div class="mb-8"><p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Relacionamento</p><h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold">Clientes</h1><p class="mt-1 text-sm text-[#746b68]">Consulte os dados e histórico de cada cliente.</p></div>
    <div class="overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_8px_24px_rgba(76,50,47,0.05)]"><div class="overflow-x-auto"><table class="w-full min-w-[650px] text-left text-sm"><thead class="bg-[#fdf8f6] text-xs font-bold uppercase tracking-wide text-[#746b68]"><tr><th class="p-4">Cliente</th><th class="p-4">E-mail</th><th class="p-4">Telefone</th><th class="p-4 text-right">Ação</th></tr></thead><tbody class="divide-y divide-[#f0e5e1]">@forelse($users as $user)<tr class="transition hover:bg-[#fdf8f6]"><td class="p-4 font-bold text-[#3e3532]">{{ $user->name }}</td><td class="p-4 text-[#625956]">{{ $user->email }}</td><td class="p-4 text-[#625956]">{{ $user->phone ?: '—' }}</td><td class="p-4 text-right"><a href="{{ route('admin.clients.show', $user) }}" class="text-xs font-bold text-[#b85d70] hover:text-[#9f4c5e]">Ver cliente</a></td></tr>@empty<tr><td colspan="4" class="p-12 text-center text-[#746b68]">Nenhum cliente cadastrado ainda.</td></tr>@endforelse</tbody></table></div></div>
@endsection
