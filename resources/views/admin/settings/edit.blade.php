@extends('layouts.admin.app')

@section('title', 'Configurações')

@section('content')
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.18em] text-[#c96f82]">Administração</p>
            <h1 class="mt-2 text-4xl">Configurações da loja</h1>
            <p class="mt-2 max-w-2xl text-sm text-[#746b68]">Controle informações comerciais e regras operacionais sem alterar o código.</p>
        </div>
        <a href="{{ route('admin.operations.index') }}" class="rounded-xl border border-[#ddc9c4] bg-white px-4 py-2.5 text-sm font-semibold text-[#544a47] hover:border-[#cf7184]">Ver central operacional</a>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="grid gap-6 xl:grid-cols-2">
        @csrf
        @method('PUT')

        <section class="rounded-2xl border border-[#eaded9] bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[.16em] text-[#c96f82]">Identidade e contato</p>
            <h2 class="mt-2 text-2xl">Informações públicas</h2>
            <div class="mt-6 space-y-5">
                <label class="block text-sm font-semibold text-[#443d3b]">Nome da loja
                    <input name="store_name" value="{{ old('store_name', $settings->store_name) }}" required class="mt-2 w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </label>
                <label class="block text-sm font-semibold text-[#443d3b]">E-mail de atendimento
                    <input type="email" name="support_email" value="{{ old('support_email', $settings->support_email) }}" class="mt-2 w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </label>
                <label class="block text-sm font-semibold text-[#443d3b]">WhatsApp
                    <input name="whatsapp" value="{{ old('whatsapp', $settings->whatsapp) }}" placeholder="5511999999999" class="mt-2 w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                    <span class="mt-1 block text-xs font-normal text-[#857b78]">Somente números, com código do país e DDD.</span>
                </label>
                <label class="block text-sm font-semibold text-[#443d3b]">Aviso no topo do site
                    <input name="announcement" value="{{ old('announcement', $settings->announcement) }}" maxlength="160" placeholder="Frete grátis acima de R$ 299" class="mt-2 w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </label>
            </div>
        </section>

        <section class="rounded-2xl border border-[#eaded9] bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[.16em] text-[#c96f82]">Comercial e operação</p>
            <h2 class="mt-2 text-2xl">Regras da loja</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <label class="block text-sm font-semibold text-[#443d3b]">Frete grátis a partir de
                    <input type="number" step="0.01" min="0" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $settings->free_shipping_threshold) }}" required class="mt-2 w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </label>
                <label class="block text-sm font-semibold text-[#443d3b]">Máximo de parcelas
                    <input type="number" min="1" max="12" name="max_installments" value="{{ old('max_installments', $settings->max_installments) }}" required class="mt-2 w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </label>
                <label class="block text-sm font-semibold text-[#443d3b] sm:col-span-2">Avisar estoque baixo quando chegar a
                    <input type="number" min="0" max="9999" name="low_stock_threshold" value="{{ old('low_stock_threshold', $settings->low_stock_threshold) }}" required class="mt-2 w-full rounded-xl border border-[#ded4d0] px-4 py-3 text-sm outline-none focus:border-[#cf7184] focus:ring-4 focus:ring-[#f7dce2]">
                </label>
            </div>

            <label class="mt-6 flex items-start gap-3 rounded-xl border border-[#eaded9] bg-[#fdf8f6] p-4">
                <input type="checkbox" name="storefront_enabled" value="1" @checked(old('storefront_enabled', $settings->storefront_enabled)) class="mt-1 h-4 w-4 accent-[#cf7184]">
                <span><strong class="block text-sm text-[#443d3b]">Loja disponível para vendas</strong><span class="mt-1 block text-xs leading-5 text-[#857b78]">Ao desativar, clientes verão uma página de manutenção. O painel administrativo continuará acessível.</span></span>
            </label>
        </section>

        @if ($errors->any())
            <div class="rounded-xl border border-[#f1c8d0] bg-[#fdf0f3] p-4 text-sm text-[#b44259] xl:col-span-2">
                <strong>Revise os campos:</strong>
                <ul class="mt-2 list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="flex justify-end xl:col-span-2">
            <button class="rounded-xl bg-[#a63f59] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#7f3047]">Salvar configurações</button>
        </div>
    </form>
@endsection
