<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->index(['active', 'created_at'], 'products_active_created_index');
            $table->index(['active', 'price'], 'products_active_price_index');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex('products_active_created_index');
            $table->dropIndex('products_active_price_index');
        });
    }
};
