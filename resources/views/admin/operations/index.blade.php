@extends('layouts.admin.app')

@section('title', 'Central operacional')

@section('content')
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.18em] text-[#c96f82]">Operação diária</p>
            <h1 class="mt-2 text-4xl">Central operacional</h1>
            <p class="mt-2 text-sm text-[#746b68]">Pendências e serviços essenciais reunidos em uma única visão.</p>
        </div>
        @if (Auth::guard('admin')->user()->role === \App\Enums\AdminRole::SuperAdmin)
            <a href="{{ route('admin.settings.edit') }}" class="rounded-xl bg-[#a63f59] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#7f3047]">Configurar loja</a>
        @endif
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Aguardando pagamento', 'value' => $pendingOrders, 'route' => route('admin.orders.index'), 'tone' => 'text-[#986d16]'],
            ['label' => 'Pagos sem envio', 'value' => $paidOrdersToPrepare, 'route' => route('admin.orders.index'), 'tone' => 'text-[#27754a]'],
            ['label' => 'Envios em andamento', 'value' => $shipmentsInProgress, 'route' => route('admin.shipments.index'), 'tone' => 'text-[#3b6199]'],
            ['label' => 'Falhas em tarefas', 'value' => $failedJobs, 'route' => route('admin.dashboard'), 'tone' => $failedJobs ? 'text-[#b44259]' : 'text-[#27754a]'],
        ] as $metric)
            <a href="{{ $metric['route'] }}" class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-[#dca3af] hover:shadow-md">
                <p class="text-sm font-medium text-[#746b68]">{{ $metric['label'] }}</p>
                <p class="mt-3 text-3xl font-bold {{ $metric['tone'] }}">{{ $metric['value'] }}</p>
                <p class="mt-2 text-xs text-[#857b78]">Abrir detalhes →</p>
            </a>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.2fr_.8fr]">
        <section class="rounded-2xl border border-[#eaded9] bg-white p-6 shadow-sm">
            <div class="flex items-end justify-between gap-4">
                <div><p class="text-xs font-bold uppercase tracking-[.16em] text-[#c96f82]">Estoque</p><h2 class="mt-2 text-2xl">Produtos que pedem atenção</h2></div>
                <span class="text-xs text-[#857b78]">Limite: {{ $settings->low_stock_threshold }}</span>
            </div>
            <div class="mt-5 divide-y divide-[#f0e5e1]">
                @forelse ($lowStockProducts as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="flex items-center justify-between gap-4 py-3 hover:bg-[#fdf8f6]">
                        <span><strong class="block text-sm text-[#3e3532]">{{ $product->name }}</strong><span class="text-xs text-[#857b78]">Revisar variantes e reposição</span></span>
                        <span class="rounded-full bg-[#fdf0f3] px-3 py-1 text-xs font-bold text-[#b44259]">{{ (int) $product->total_stock }} un.</span>
                    </a>
                @empty
                    <p class="rounded-xl bg-[#eaf6ef] p-5 text-center text-sm text-[#27754a]">Nenhum produto abaixo do limite configurado.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-[#eaded9] bg-white p-6 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-[.16em] text-[#c96f82]">Serviços</p>
            <h2 class="mt-2 text-2xl">Integrações configuradas</h2>
            <p class="mt-2 text-xs leading-5 text-[#857b78]">A verificação confirma a presença da configuração, sem expor chaves ou fazer cobranças externas.</p>
            <div class="mt-5 space-y-2">
                @foreach ($integrations as $integration)
                    <div class="flex items-center justify-between rounded-xl border border-[#eaded9] px-4 py-3">
                        <span class="text-sm font-semibold text-[#443d3b]">{{ $integration['name'] }}</span>
                        <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase {{ $integration['configured'] ? 'bg-[#eaf6ef] text-[#27754a]' : 'bg-[#fff6df] text-[#986d16]' }}">{{ $integration['configured'] ? 'Configurado' : 'Revisar' }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
