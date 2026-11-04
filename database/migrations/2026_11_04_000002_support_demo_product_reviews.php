<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->boolean('is_demo')->default(false)->after('comment');
            $table->dropForeign(['order_item_id']);
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('order_item_id')->nullable()->change();
            $table->foreign('order_item_id')->references('id')->on('order_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        DB::table('product_reviews')->where('is_demo', true)->delete();

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('order_item_id')->nullable(false)->change();
            $table->foreign('order_item_id')->references('id')->on('order_items')->cascadeOnDelete();
            $table->dropColumn('is_demo');
        });
    }
};
