<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('label')
                ->nullable();

            $table->string('recipient_name');

            $table->string('phone')
                ->nullable();

            $table->string('cpf', 14)
                ->nullable();

            $table->string('street');

            $table->string('number');

            $table->string('complement')
                ->nullable();

            $table->string('neighborhood')
                ->nullable();

            $table->string('city');

            $table->string('state', 2);

            $table->string('cep');

            $table->boolean('is_default')
                ->default(false);

            $table->timestamps();

            $table->index('user_id');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
