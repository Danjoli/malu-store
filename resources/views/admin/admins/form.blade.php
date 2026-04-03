@csrf

@if($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div>
    <label class="block mb-1 font-semibold">Nome</label>
    <input type="text" name="name"
           value="{{ old('name', $admin->name ?? '') }}"
           class="w-full border p-2 rounded">
</div>

<div>
    <label class="block mb-1 font-semibold">Email</label>
    <input type="email" name="email"
           value="{{ old('email', $admin->email ?? '') }}"
           class="w-full border p-2 rounded">
</div>

<div>
    <label class="block mb-1 font-semibold">Senha</label>
    <input type="password" name="password"
           class="w-full border p-2 rounded">
    <small class="text-gray-500">Preencha apenas se quiser alterar</small>
</div>

{{-- CARGO --}}
<div>
    <label class="block mb-1 font-semibold">Cargo</label>
    <select name="role" class="w-full border p-2 rounded">
        @foreach($roles as $role)
            <option value="{{ $role }}"
                {{ old('role', $admin->role ?? 'admin') == $role ? 'selected' : '' }}>
                {{ ucfirst($role) }}
            </option>
        @endforeach
    </select>
</div>

{{-- ATIVO / INATIVO --}}
<div class="flex items-center gap-2 mt-2">
    <input type="checkbox" name="is_active" value="1"
        {{ old('is_active', $admin->is_active ?? true) ? 'checked' : '' }}>
    <label>Administrador ativo</label>
</div>
