@csrf

@if ($errors->any())
    <div
        class="mb-4 rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] p-4 text-sm text-[#b44259]"
        role="alert"
    >
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div>
    <label class="mb-2 block text-sm font-semibold text-[#443d3b]">
        Nome
    </label>

    <input
        type="text"
        name="name"
        value="{{ old('name', $admin->name ?? '') }}"
        class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
    >
</div>

<div>
    <label class="mb-2 block text-sm font-semibold text-[#443d3b]">
        E-mail
    </label>

    <input
        type="email"
        name="email"
        value="{{ old('email', $admin->email ?? '') }}"
        class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
    >
</div>

<div>
    <label class="mb-2 block text-sm font-semibold text-[#443d3b]">
        Senha
    </label>

    <input
        type="password"
        name="password"
        class="w-full rounded-xl border border-[#ded4d0] px-4 py-3 outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
    >

    <small class="mt-2 block text-xs text-[#857b78]">
        Preencha apenas se quiser alterar.
    </small>
</div>

{{-- Cargo --}}
<div>
    <label class="mb-2 block text-sm font-semibold text-[#443d3b]">
        Cargo
    </label>

    <select
        name="role"
        class="w-full rounded-xl border border-[#ded4d0] bg-white px-4 py-3 outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
    >
        @foreach ($roles as $role)
            <option
                value="{{ $role->value }}"
                @selected(old('role', $admin->role?->value ?? 'admin') === $role->value)
            >
                {{ $role->label() }}
            </option>
        @endforeach
    </select>
</div>

{{-- Ativo / Inativo --}}
<div class="mt-2 flex items-center gap-2 text-sm text-[#443d3b]">
    <input
        type="checkbox"
        id="is_active"
        name="is_active"
        value="1"
        @checked(old('is_active', $admin->is_active ?? true))
    >

    <label for="is_active">
        Administrador ativo
    </label>
</div>
