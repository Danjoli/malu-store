@extends('layouts.admin.app')

@section('title', 'Administradores')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Equipe</p>
            <h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold">Administradores</h1>
            <p class="mt-1 text-sm text-[#746b68]">Gerencie os acessos ao painel.</p>
        </div><a href="{{ route('admin.admins.create') }}"
            class="rounded-xl bg-[#cf7184] px-4 py-3 text-center text-sm font-bold text-white hover:bg-[#b85d70]">+ Novo
            administrador</a>
    </div>
    <div class="overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-[#fdf8f6] text-xs font-bold uppercase tracking-wide text-[#746b68]">
                    <tr>
                        <th class="p-4">Nome</th>
                        <th class="p-4">E-mail</th>
                        <th class="p-4">Cargo</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f0e5e1]">
                    @forelse($admins as $admin)
                        @php($roles = ['superadmin' => 'Superadmin', 'admin' => 'Admin', 'suporte' => 'Suporte'])
                        <tr class="transition hover:bg-[#fdf8f6]">
                            <td class="p-4 font-bold text-[#3e3532]">{{ $admin->name }}</td>
                            <td class="p-4 text-[#625956]">{{ $admin->email }}</td>
                            <td class="p-4"><span
                                    class="rounded-full bg-[#f8f3f1] px-2.5 py-1 text-xs font-bold text-[#625956]">{{ $roles[$admin->role] ?? ucfirst($admin->role) }}</span>
                            </td>
                            <td class="p-4"><span
                                    class="rounded-full px-2.5 py-1 text-xs font-bold {{ $admin->is_active ? 'bg-[#eaf6ef] text-[#27754a]' : 'bg-[#fdf0f3] text-[#b44259]' }}">{{ $admin->is_active ? 'Ativo' : 'Inativo' }}</span>
                            </td>
                            <td class="p-4 text-right"><a href="{{ route('admin.admins.show', $admin) }}"
                                    class="text-xs font-bold text-[#625956] hover:text-[#b85d70]">Ver</a><a
                                    href="{{ route('admin.admins.edit', $admin) }}"
                                    class="ml-3 text-xs font-bold text-[#b85d70]">Editar</a>
                                <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST"
                                    class="ml-3 inline">@csrf @method('DELETE')<button type="submit"
                                        class="text-xs font-bold text-[#b44259]"
                                        onclick="return confirm('Tem certeza que deseja excluir este administrador?')">Excluir</button>
                                </form>
                            </td>
                    </tr>@empty<tr>
                            <td colspan="5" class="p-12 text-center text-[#746b68]">Nenhum administrador cadastrado
                                ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
