<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('order_items', 'category_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('product_id');
            });

            // Add foreign key only if categories table exists (some environments may not have it)
            if (Schema::hasTable('categories')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order_items', 'category_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }
    }
};
