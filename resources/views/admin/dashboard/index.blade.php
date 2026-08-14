@extends('layouts.admin.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">
                Visão geral
            </p>

            <h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold text-[#2d2928]">
                Dashboard
            </h1>

            <p class="mt-1 text-sm text-[#746b68]">
                Acompanhe o movimento da Malu Store.
            </p>
        </div>

        <a
            href="{{ route('admin.orders.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-[#ddc9c4] bg-white px-4 py-2.5 text-sm font-semibold text-[#544a47] transition hover:border-[#cf7184] hover:text-[#b85d70]"
        >
            Ver pedidos
        </a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
            <p class="text-sm font-medium text-[#746b68]">
                Vendas totais
            </p>

            <p class="mt-3 text-2xl font-bold tracking-tight text-[#2d2928]">
                R$ {{ number_format($totalSalesOverall, 2, ',', '.') }}
            </p>

            <span class="mt-4 inline-flex rounded-full bg-[#fdf0f3] px-2.5 py-1 text-xs font-semibold text-[#b85d70]">
                Faturamento geral
            </span>
        </article>

        <article class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
            <p class="text-sm font-medium text-[#746b68]">
                Vendas deste mês
            </p>

            <p class="mt-3 text-2xl font-bold tracking-tight text-[#2d2928]">
                R$ {{ number_format($salesThisMonth, 2, ',', '.') }}
            </p>

            <span class="mt-4 inline-flex rounded-full bg-[#f8f3f1] px-2.5 py-1 text-xs font-semibold text-[#746b68]">
                {{ now()->locale('pt_BR')->translatedFormat('F') }}
            </span>
        </article>

        <article class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
            <p class="text-sm font-medium text-[#746b68]">
                Pedidos
            </p>

            <p class="mt-3 text-2xl font-bold tracking-tight text-[#2d2928]">
                {{ $totalOrders }}
            </p>

            <span class="mt-4 inline-flex rounded-full bg-[#f8f3f1] px-2.5 py-1 text-xs font-semibold text-[#746b68]">
                Todos os pedidos
            </span>
        </article>

        <article class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
            <p class="text-sm font-medium text-[#746b68]">
                Clientes
            </p>

            <p class="mt-3 text-2xl font-bold tracking-tight text-[#2d2928]">
                {{ $totalClients }}
            </p>

            <span class="mt-4 inline-flex rounded-full bg-[#f8f3f1] px-2.5 py-1 text-xs font-semibold text-[#746b68]">
                Cadastros ativos
            </span>
        </article>

        <article class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
            <p class="text-sm font-medium text-[#746b68]">
                Produtos
            </p>

            <p class="mt-3 text-2xl font-bold tracking-tight text-[#2d2928]">
                {{ $totalProducts }}
            </p>

            <span class="mt-4 inline-flex rounded-full bg-[#f8f3f1] px-2.5 py-1 text-xs font-semibold text-[#746b68]">
                No catálogo
            </span>
        </article>

        <article class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)]">
            <p class="text-sm font-medium text-[#746b68]">
                Envios realizados
            </p>

            <p class="mt-3 text-2xl font-bold tracking-tight text-[#2d2928]">
                {{ $totalShipped }}
            </p>

            <span class="mt-4 inline-flex rounded-full bg-[#f8f3f1] px-2.5 py-1 text-xs font-semibold text-[#746b68]">
                Em trânsito ou enviados
            </span>
        </article>
    </div>

    <div class="mt-4 grid gap-4 sm:grid-cols-3">
        <a
            href="{{ route('admin.orders.index') }}"
            class="rounded-2xl border border-[#eaded9] bg-white p-5 transition hover:border-[#dca3af] hover:shadow-[0_8px_24px_rgba(76,50,47,0.08)]"
        >
            <p class="text-sm font-medium text-[#746b68]">
                Aguardando pagamento
            </p>

            <p class="mt-2 text-2xl font-bold text-[#986d16]">
                {{ $pendingOrders }}
            </p>

            <p class="mt-2 text-xs text-[#857b78]">
                Pedidos que precisam de atenção
            </p>
        </a>

        <a
            href="{{ route('admin.orders.index') }}"
            class="rounded-2xl border border-[#eaded9] bg-white p-5 transition hover:border-[#dca3af] hover:shadow-[0_8px_24px_rgba(76,50,47,0.08)]"
        >
            <p class="text-sm font-medium text-[#746b68]">
                Pedidos pagos
            </p>

            <p class="mt-2 text-2xl font-bold text-[#27754a]">
                {{ $paidOrders }}
            </p>

            <p class="mt-2 text-xs text-[#857b78]">
                Prontos para acompanhamento
            </p>
        </a>

        <a
            href="{{ route('admin.products.index') }}"
            class="rounded-2xl border border-[#eaded9] bg-white p-5 transition hover:border-[#dca3af] hover:shadow-[0_8px_24px_rgba(76,50,47,0.08)]"
        >
            <p class="text-sm font-medium text-[#746b68]">
                Estoque baixo
            </p>

            <p class="mt-2 text-2xl font-bold {{ $lowStockProducts ? 'text-[#b44259]' : 'text-[#27754a]' }}">
                {{ $lowStockProducts }}
            </p>

            <p class="mt-2 text-xs text-[#857b78]">
                Produtos com até 5 unidades
            </p>
        </a>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
        <section class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)] sm:p-6">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold text-[#2d2928]">
                        Vendas mensais
                    </h2>

                    <p class="mt-1 text-sm text-[#746b68]">
                        Faturamento por mês em {{ now()->year }}.
                    </p>
                </div>
            </div>

            <div class="h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </section>

        <section class="rounded-2xl border border-[#eaded9] bg-white p-5 shadow-[0_8px_24px_rgba(76,50,47,0.05)] sm:p-6">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="font-['Cormorant_Garamond'] text-2xl font-semibold text-[#2d2928]">
                        Pedidos recentes
                    </h2>

                    <p class="mt-1 text-sm text-[#746b68]">
                        Últimas movimentações.
                    </p>
                </div>

                <a
                    href="{{ route('admin.orders.index') }}"
                    class="text-xs font-bold text-[#b85d70] hover:text-[#9f4c5e]"
                >
                    Ver todos
                </a>
            </div>

            <div class="divide-y divide-[#f0e5e1]">
                @forelse ($recentOrders as $order)
                    <a
                        href="{{ route('admin.orders.show', $order) }}"
                        class="flex items-center justify-between gap-3 py-3 transition hover:bg-[#fdf8f6]"
                    >
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#3e3532]">
                                Pedido #{{ $order->id }}
                            </p>

                            <p class="truncate text-xs text-[#857b78]">
                                {{ $order->user->name }}
                            </p>
                        </div>

                        <p class="shrink-0 text-sm font-bold text-[#2d2928]">
                            R$ {{ number_format($order->total, 2, ',', '.') }}
                        </p>
                    </a>
                @empty
                    <p class="rounded-xl bg-[#fdf8f6] px-4 py-8 text-center text-sm text-[#746b68]">
                        Ainda não há pedidos.
                    </p>
                @endforelse
            </div>
        </section>
    </div>

    <script>
        window.DASHBOARD = {
            months: @json($months),
            sales: @json($sales),
        };
    </script>
@endsection
