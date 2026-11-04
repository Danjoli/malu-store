<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OperationsController extends Controller
{
    public function index(): View
    {
        $settings = StoreSetting::current();
        $threshold = (int) ($settings->low_stock_threshold ?? 5);

        $lowStockProducts = Product::query()
            ->withSum('variants as total_stock', 'stock')
            ->has('variants')
            ->whereRaw('(select coalesce(sum(product_variants.stock), 0) from product_variants where product_variants.product_id = products.id) <= ?', [$threshold])
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('admin.operations.index', [
            'settings' => $settings,
            'pendingOrders' => Order::query()->whereIn('status', [OrderStatus::Pending->value, OrderStatus::PendingPayment->value])->count(),
            'paidOrdersToPrepare' => Order::query()->where('status', OrderStatus::Paid->value)->whereDoesntHave('shipment')->count(),
            'shipmentsInProgress' => Shipment::query()->whereIn('status', [
                ShipmentStatus::Pending->value,
                ShipmentStatus::Processing->value,
                ShipmentStatus::WaitingPost->value,
                ShipmentStatus::Shipped->value,
                ShipmentStatus::InTransit->value,
            ])->count(),
            'lowStockProducts' => $lowStockProducts,
            'failedJobs' => DB::table('failed_jobs')->count(),
            'integrations' => [
                ['name' => 'Redis / cache', 'configured' => config('cache.default') === 'redis' && filled(config('database.redis.default.host'))],
                ['name' => 'Cloudflare R2', 'configured' => filled(config('filesystems.disks.s3.key')) && filled(config('filesystems.disks.s3.bucket'))],
                ['name' => 'Asaas', 'configured' => filled(config('services.asaas.api_key'))],
                ['name' => 'Melhor Envio', 'configured' => filled(config('services.melhor_envio.token'))],
                ['name' => 'Sentry', 'configured' => filled(config('sentry.dsn'))],
                ['name' => 'E-mail', 'configured' => config('mail.default') !== 'log'],
            ],
        ]);
    }
}
