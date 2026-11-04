<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'details')) {
            Schema::table('products', function (Blueprint $table) {
                $table->text('details')->nullable()->after('description');
            });
        }

        if (! Schema::hasColumn('products', 'composition')) {
            Schema::table('products', function (Blueprint $table) {
                $table->text('composition')->nullable()->after('details');
            });
        }

        if (! Schema::hasColumn('order_items', 'product_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            });
        }

        DB::table('order_items')
            ->whereNull('product_id')
            ->whereNotNull('product_variant_id')
            ->orderBy('id')
            ->eachById(function ($item): void {
                $productId = DB::table('product_variants')->where('id', $item->product_variant_id)->value('product_id');

                if ($productId) {
                    DB::table('order_items')->where('id', $item->id)->update(['product_id' => $productId]);
                }
            });

        if (! Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('order_item_id')->unique()->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->string('title', 80)->nullable();
                $table->text('comment');
                $table->timestamps();

                $table->index(['product_id', 'created_at']);
                $table->index(['product_id', 'rating']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');

        if (Schema::hasColumn('order_items', 'product_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropConstrainedForeignId('product_id');
            });
        }

        foreach (['composition', 'details'] as $column) {
            if (Schema::hasColumn('products', $column)) {
                Schema::table('products', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
