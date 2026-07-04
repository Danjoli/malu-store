<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_variant_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name_snapshot');

            $table->string('image_snapshot');

            $table->string('color_snapshot')
                ->nullable();

            $table->string('size_snapshot')
                ->nullable();

            $table->decimal('price', 10, 2);

            $table->unsignedInteger('quantity');

            $table->timestamps();

            $table->unique([
                'cart_id',
                'product_variant_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
