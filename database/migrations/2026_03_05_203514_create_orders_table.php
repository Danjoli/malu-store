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

            // Snapshot do endereço de entrega
            $table->string('recipient_name');
            $table->string('phone')->nullable();
            $table->string('cpf', 14)->nullable();

            $table->string('street');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city');
            $table->string('state', 2);
            $table->string('cep');

            // Valores do pedido
            $table->decimal('subtotal', 10, 2);

            $table->decimal('shipping', 10, 2)
                ->default(0.00);

            $table->decimal('total', 10, 2);

            // Status
            $table->enum('status', [
                'pending',
                'pending_payment',
                'paid',
                'failed',
                'expired',
                'cancelled',
                'shipped',
                'delivered'
            ])->default('pending');

            // Pagamento
            $table->string('payment_method')->nullable();
            $table->string('gateway_payment_id')->nullable();
            $table->string('gateway_status')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->dateTime('expires_at')->nullable();

            $table->index('user_id');
            $table->index('status');
            $table->index('gateway_payment_id');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
