<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('shipment_id', 100)->nullable()->index();
            $table->string('service_id', 100)->nullable();
            $table->string('carrier', 100)->nullable();

            $table->string('tracking_code', 255)->nullable();

            $table->decimal('shipping_cost', 10, 2)->default(0.00);

            $table->string('status', 50)->default('pending');

            $table->longText('last_update')->nullable();

            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->text('label_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
