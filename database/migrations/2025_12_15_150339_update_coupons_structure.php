<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCouponsStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
       public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            if (!Schema::hasColumn('coupons', 'max_discount_value')) {
                $table->decimal('max_discount_value', 15, 2)->nullable()->after('value')
                      ->comment('Số tiền giảm tối đa (chỉ dùng cho %)');
            }

            if (!Schema::hasColumn('coupons', 'min_order_value')) {
                $table->decimal('min_order_value', 15, 2)->default(0)->after('value');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['max_discount_value']);
        });
    }
}
