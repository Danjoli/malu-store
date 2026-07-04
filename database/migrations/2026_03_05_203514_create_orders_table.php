<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('address_id')
                ->constrained()
                ->restrictOnDelete();

            $table->decimal('subtotal', 10, 2);

            $table->decimal('shipping', 10, 2)
                ->default(0.00);

            $table->decimal('total', 10, 2);

            $table->enum('status', [
                'pending',
                'pending_payment',
                'paid',
                'cancelled',
                'shipped',
                'delivered'
            ])->default('pending');

            $table->string('payment_method')
                ->nullable();

            $table->string('gateway_payment_id')
                ->nullable();

            $table->string('gateway_status')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();

            $table->dateTime('expires_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
