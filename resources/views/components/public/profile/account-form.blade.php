@props(['user'])

<h2 class="text-xl font-semibold mb-6">
    Editar Conta
</h2>

<form method="POST" action="{{ route('profile.update') }}">

    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="text-sm text-gray-600">
            Nome
        </label>

        <input
            type="text"
            name="name"
            value="{{ $user->name }}"
            class="w-full border rounded p-2 mt-1"
        >
    </div>

    <div class="mb-4">
        <label class="text-sm text-gray-600">
            Email
        </label>

        <input
            type="email"
            name="email"
            value="{{ $user->email }}"
            class="w-full border rounded p-2 mt-1"
        >
    </div>

    <div class="mb-4">
        <label class="text-sm text-gray-600">
            Telefone
        </label>

        <input
            type="text"
            name="phone"
            value="{{ $user->phone }}"
            class="w-full border rounded p-2 mt-1"
        >
    </div>

    <button class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded">
        Salvar Alterações
    </button>

</form>
