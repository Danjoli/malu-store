@extends('layouts.admin.app')

@section('title', 'Pedidos')

@section('content')
    <div class="mb-8"><p class="text-xs font-bold uppercase tracking-[0.18em] text-[#c96f82]">Vendas</p><h1 class="mt-2 font-['Cormorant_Garamond'] text-4xl font-semibold">Pedidos</h1><p class="mt-1 text-sm text-[#746b68]">Acompanhe pagamentos e entregas.</p></div>
    <div class="overflow-hidden rounded-2xl border border-[#eaded9] bg-white shadow-[0_8px_24px_rgba(76,50,47,0.05)]"><div class="overflow-x-auto"><table class="w-full min-w-[720px] text-left text-sm"><thead class="bg-[#fdf8f6] text-xs font-bold uppercase tracking-wide text-[#746b68]"><tr><th class="p-4">Pedido</th><th class="p-4">Cliente</th><th class="p-4">Total</th><th class="p-4">Status</th><th class="p-4">Data</th><th class="p-4 text-right">Ação</th></tr></thead><tbody class="divide-y divide-[#f0e5e1]">
    @forelse($orders as $order) @php($statuses = ['pending' => ['Aguardando pagamento','bg-[#fff6df] text-[#986d16]'], 'paid' => ['Pago','bg-[#eaf6ef] text-[#27754a]'], 'shipped' => ['Enviado','bg-[#edf4ff] text-[#3b6199]'], 'delivered' => ['Entregue','bg-[#f1edfb] text-[#69549c]'], 'cancelled' => ['Cancelado','bg-[#fdf0f3] text-[#b44259]']]) @php([$label,$class] = $statuses[$order->status] ?? [$order->status, 'bg-[#f8f3f1] text-[#746b68]'])
    <tr class="transition hover:bg-[#fdf8f6]"><td class="p-4 font-bold text-[#3e3532]">#{{ $order->id }}</td><td class="p-4 text-[#625956]">{{ $order->user->name }}</td><td class="p-4 font-bold">R$ {{ number_format($order->total,2,',','.') }}</td><td class="p-4"><span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $class }}">{{ $label }}</span></td><td class="p-4 text-[#746b68]">{{ $order->created_at->format('d/m/Y H:i') }}</td><td class="p-4 text-right"><a href="{{ route('admin.orders.show',$order) }}" class="text-xs font-bold text-[#b85d70] hover:text-[#9f4c5e]">Ver pedido</a></td></tr>
    @empty<tr><td colspan="6" class="p-12 text-center text-[#746b68]">Nenhum pedido encontrado.</td></tr>@endforelse
    </tbody></table></div></div><div class="mt-6">{{ $orders->links() }}</div>
@endsection
