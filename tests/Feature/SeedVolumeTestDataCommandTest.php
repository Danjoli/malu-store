<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SeedVolumeTestDataCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_synthetic_users_products_and_variants_in_batches(): void
    {
        $this->artisan('staging:seed-volume', ['--users' => 12, '--products' => 8, '--batch' => 100])
            ->assertSuccessful();

        $this->assertSame(12, DB::table('users')->where('email', 'like', 'volume-%@example.invalid')->count());
        $this->assertSame(8, DB::table('products')->where('slug', 'like', 'volume-produto-%')->count());
        $this->assertSame(8, DB::table('product_variants')->where('color', 'Teste')->where('size', 'U')->count());
    }

    public function test_it_is_resumable_and_targets_totals_instead_of_adding_duplicates(): void
    {
        $arguments = ['--users' => 5, '--products' => 4, '--batch' => 100];

        $this->artisan('staging:seed-volume', $arguments)->assertSuccessful();
        $this->artisan('staging:seed-volume', $arguments)->assertSuccessful();

        $this->assertSame(5, DB::table('users')->where('email', 'like', 'volume-%@example.invalid')->count());
        $this->assertSame(4, DB::table('products')->where('slug', 'like', 'volume-produto-%')->count());
        $this->assertSame(4, DB::table('product_variants')->where('color', 'Teste')->where('size', 'U')->count());
    }

    public function test_it_refuses_to_run_in_production(): void
    {
        app()->detectEnvironment(fn (): string => 'production');

        $this->artisan('staging:seed-volume', ['--users' => 1, '--products' => 1, '--batch' => 100])
            ->assertFailed();

        $this->assertSame(0, DB::table('users')->count());
        $this->assertSame(0, DB::table('products')->count());
    }
}
