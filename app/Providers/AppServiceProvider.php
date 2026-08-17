<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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
        Blade::anonymousComponentPath(
            resource_path('views/components/public'),
            'public'
        );

        View::composer('layouts.public.partials.header', function ($view) {
            $cartItemCount = 0;
            $favoritesCount = 0;

            if (Auth::check()) {
                /** @var User $user */
                $user = Auth::user();

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
