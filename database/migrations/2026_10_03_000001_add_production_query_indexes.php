<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'created_at'], 'orders_user_status_created_index');
            $table->index(['status', 'created_at'], 'orders_status_created_index');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'carts_user_status_index');
        });

    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_user_status_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_status_created_index');
            $table->dropIndex('orders_status_created_index');
        });
    }
};
