@props(['user'])

<h2 class="store-title mb-6 text-2xl font-semibold">
    Editar Conta
</h2>

<form
    method="POST"
    action="{{ route('profile.update') }}"
>
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="text-xs font-medium text-stone-700">
            Nome
        </label>

        <input
            type="text"
            name="name"
            value="{{ $user->name }}"
            class="store-input mt-1"
        >
    </div>

    <div class="mb-4">
        <label class="text-xs font-medium text-stone-700">
            Email
        </label>

        <input
            type="email"
            name="email"
            value="{{ $user->email }}"
            class="store-input mt-1"
        >
    </div>

    <div class="mb-4">
        <label class="text-xs font-medium text-stone-700">
            Telefone
        </label>

        <input
            type="text"
            name="phone"
            value="{{ $user->phone }}"
            class="store-input mt-1"
        >
    </div>

    <button
        type="submit"
        class="store-button store-button-primary"
    >
        Salvar Alterações
    </button>
</form>
