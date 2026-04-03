<h2 class="text-xl font-semibold mb-6">
    Alterar Senha
</h2>

<form method="POST" action="{{ route('profile.password.update') }}">

    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="text-sm text-gray-600">
            Senha Atual
        </label>

        <input
            type="password"
            name="current_password"
            class="w-full border rounded p-2 mt-1"
        >
    </div>

    <div class="mb-4">
        <label class="text-sm text-gray-600">
            Nova Senha
        </label>

        <input
            type="password"
            name="password"
            class="w-full border rounded p-2 mt-1"
        >
    </div>

    <div class="mb-4">
        <label class="text-sm text-gray-600">
            Confirmar Nova Senha
        </label>

        <input
            type="password"
            name="password_confirmation"
            class="w-full border rounded p-2 mt-1"
        >
    </div>

    <button class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded">
        Atualizar Senha
    </button>

</form>
