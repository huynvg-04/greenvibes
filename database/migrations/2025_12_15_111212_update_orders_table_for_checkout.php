<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersTableForCheckout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Thêm mã đơn hàng để tra cứu (VD: ORD-8888)
            if (!Schema::hasColumn('orders', 'code')) $table->string('code')->unique()->after('id');

            // Thông tin người nhận
            if (!Schema::hasColumn('orders', 'phone')) $table->string('phone', 20)->after('shipping_address');
            if (!Schema::hasColumn('orders', 'note')) $table->text('note')->nullable()->after('status');

            // Thanh toán
            if (!Schema::hasColumn('orders', 'payment_method')) $table->string('payment_method')->default('cod')->after('total_amount');
            if (!Schema::hasColumn('orders', 'discount_amount')) $table->decimal('discount_amount', 15, 2)->default(0)->after('total_amount');
            if (!Schema::hasColumn('orders', 'coupon_code')) $table->string('coupon_code')->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['code', 'phone', 'note', 'payment_method', 'discount_amount', 'coupon_code']);
        });
    }
}
