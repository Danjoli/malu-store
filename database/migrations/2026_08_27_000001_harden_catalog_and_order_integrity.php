<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unique(['product_id', 'color', 'size']);
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->unique('order_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['gateway_payment_id']);
            $table->unique('gateway_payment_id');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->cascadeOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['gateway_payment_id']);
            $table->index('gateway_payment_id');
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'color', 'size']);
        });
    }
};
