<h2 class="store-title mb-6 text-2xl font-semibold">
    Alterar Senha
</h2>

<form method="POST" action="{{ route('profile.password.update') }}">

    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="text-xs font-medium text-stone-700">
            Senha Atual
        </label>

        <input
            type="password"
            name="current_password"
            class="store-input mt-1"
        >
    </div>

    <div class="mb-4">
        <label class="text-xs font-medium text-stone-700">
            Nova Senha
        </label>

        <input
            type="password"
            name="password"
            class="store-input mt-1"
        >
    </div>

    <div class="mb-4">
        <label class="text-xs font-medium text-stone-700">
            Confirmar Nova Senha
        </label>

        <input
            type="password"
            name="password_confirmation"
            class="store-input mt-1"
        >
    </div>

    <button class="store-button store-button-primary">
        Atualizar Senha
    </button>

</form>
