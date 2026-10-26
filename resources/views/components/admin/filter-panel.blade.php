@props(['action', 'reset'])

<form method="GET" action="{{ $action }}" class="mb-5 rounded-2xl border border-[#eaded9] bg-white p-4 shadow-[0_8px_24px_rgba(76,50,47,0.04)]">
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        {{ $slot }}
    </div>
    <div class="mt-3 flex flex-wrap items-center gap-3">
        <button class="rounded-xl bg-[#cf7184] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#b85d70]">Aplicar filtros</button>
        <a href="{{ $reset }}" class="text-sm font-bold text-[#746b68] hover:text-[#b85d70]">Limpar filtros</a>
    </div>
</form>
