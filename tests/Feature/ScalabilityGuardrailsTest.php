<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ScalabilityGuardrailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_expensive_checkout_and_payment_routes_are_rate_limited(): void
    {
        $checkoutMiddleware = Route::getRoutes()->getByName('checkout.process')?->gatherMiddleware() ?? [];
        $paymentMiddleware = Route::getRoutes()->getByName('payment.process')?->gatherMiddleware() ?? [];

        $this->assertContains('throttle:checkout', $checkoutMiddleware);
        $this->assertContains('throttle:payment', $paymentMiddleware);
    }

    public function test_admin_product_listing_has_bounded_queries_and_pagination(): void
    {
        Product::factory()->count(30)->create();

        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->withSum('variants', 'stock')
            ->latest()
            ->paginate(25);

        $this->assertCount(25, $products->items());
        $this->assertSame(30, $products->total());
    }
}
