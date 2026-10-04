<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductStorageMigrationTest extends TestCase
{
    public function test_product_files_are_copied_verified_and_kept_at_source(): void
    {
        Storage::fake('public');
        Storage::fake('s3');
        Storage::disk('public')->put('products/example.png', 'image-content');

        $this->artisan('storage:migrate-products', ['--execute' => true])
            ->expectsOutputToContain('Verified: products/example.png')
            ->assertSuccessful();

        Storage::disk('public')->assertExists('products/example.png');
        Storage::disk('s3')->assertExists('products/example.png');
        $this->assertSame(
            Storage::disk('public')->get('products/example.png'),
            Storage::disk('s3')->get('products/example.png'),
        );
    }

    public function test_dry_run_does_not_copy_files(): void
    {
        Storage::fake('public');
        Storage::fake('s3');
        Storage::disk('public')->put('products/example.png', 'image-content');

        $this->artisan('storage:migrate-products')->assertSuccessful();

        Storage::disk('s3')->assertMissing('products/example.png');
    }
}
