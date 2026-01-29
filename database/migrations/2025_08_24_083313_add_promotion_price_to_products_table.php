<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionPriceToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('products', 'promotion_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('promotion_price', 15, 2)->nullable()->after('price');
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
        if (Schema::hasColumn('products', 'promotion_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('promotion_price');
            });
        }
    }
}
