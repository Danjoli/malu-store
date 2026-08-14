<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(
            resource_path('views/components/public'),
            'public'
        );

        View::composer('layouts.public.partials.header', function ($view) {
            $cartItemCount = 0;

            if (auth()->check()) {
                $cartItemCount = (int) Cart::where('user_id', auth()->id())
                    ->where('status', 'active')
                    ->withSum('items', 'quantity')
                    ->value('items_sum_quantity');
            }

            $view->with('cartItemCount', $cartItemCount);
        });
    }
}
