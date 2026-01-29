<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionIdToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('products', 'promotion_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedBigInteger('promotion_id')->nullable()->after('category_id');
                $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('products', 'promotion_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['promotion_id']);
                $table->dropColumn('promotion_id');
            });
        }
    }
}
