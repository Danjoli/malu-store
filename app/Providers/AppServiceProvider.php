<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\User;
use App\Observers\AdminAuditObserver;
use App\Observers\CategoryObserver;
use App\Observers\ProductObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Category::observe(CategoryObserver::class);
        Product::observe(ProductObserver::class);
        Admin::observe(AdminAuditObserver::class);
        Category::observe(AdminAuditObserver::class);
        Product::observe(AdminAuditObserver::class);
        ProductVariant::observe(AdminAuditObserver::class);
        Shipment::observe(AdminAuditObserver::class);

        RateLimiter::for('checkout', fn (Request $request) => Limit::perMinute(10)
            ->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip())));
        RateLimiter::for('payment', fn (Request $request) => Limit::perMinute(15)
            ->by((string) ($request->user()?->getAuthIdentifier() ?? $request->ip())));

        Blade::anonymousComponentPath(
            resource_path('views/components/public'),
            'public'
        );

        View::composer('layouts.public.partials.header', function ($view) {
            $cartItemCount = 0;
            $favoritesCount = 0;

            // O cabeçalho público sempre usa o guard do cliente, inclusive nas páginas de erro.
            if (Auth::guard('web')->check()) {
                /** @var User $user */
                $user = Auth::guard('web')->user();

                $cartItemCount = (int) Cart::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->withSum('items', 'quantity')
                    ->value('items_sum_quantity');

                $favoritesCount = $user->favorites()->count();
            }

            $view->with([
                'cartItemCount' => $cartItemCount,
                'favoritesCount' => $favoritesCount,
            ]);
        });
    }
}
