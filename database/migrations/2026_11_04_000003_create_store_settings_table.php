<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Malu Store');
            $table->string('support_email')->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->decimal('free_shipping_threshold', 10, 2)->default(299);
            $table->unsignedTinyInteger('max_installments')->default(6);
            $table->unsignedSmallInteger('low_stock_threshold')->default(5);
            $table->boolean('storefront_enabled')->default(true);
            $table->string('announcement')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
