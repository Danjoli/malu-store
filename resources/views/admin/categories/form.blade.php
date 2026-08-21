@csrf

@if ($errors->any())
    <div
        class="rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] px-4 py-3 text-sm text-[#b44259]"
        role="alert"
    >
        <p class="mb-2 font-bold">
            Verifique os campos abaixo:
        </p>

        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Nome --}}
<div>
    <label
        for="name"
        class="mb-2 block text-sm font-semibold text-[#443d3b]"
    >
        Nome da categoria
    </label>

    <input
        id="name"
        type="text"
        name="name"
        value="{{ old('name', $category->name ?? '') }}"
        placeholder="Ex.: Vestidos"
        autocomplete="off"
        required
        class="w-full rounded-xl border border-[#ded4d0] bg-white px-4 py-3 text-sm text-[#443d3b] outline-none transition placeholder:text-[#a99b98] focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
    >

    <p class="mt-2 text-xs text-[#857b78]">
        Nome que será exibido para os clientes na loja.
    </p>
</div>

{{-- Slug --}}
<div>
    <label
        for="slug"
        class="mb-2 block text-sm font-semibold text-[#443d3b]"
    >
        Slug
    </label>

    <input
        id="slug"
        type="text"
        name="slug"
        value="{{ old('slug', $category->slug ?? '') }}"
        placeholder="Ex.: vestidos"
        autocomplete="off"
        class="w-full rounded-xl border border-[#ded4d0] bg-white px-4 py-3 font-mono text-sm text-[#443d3b] outline-none transition placeholder:text-[#a99b98] focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]"
    >

    <p class="mt-2 text-xs leading-5 text-[#857b78]">
        Usado na URL da categoria. Se deixar em branco, ele será criado automaticamente a partir do nome.
    </p>
</div>
