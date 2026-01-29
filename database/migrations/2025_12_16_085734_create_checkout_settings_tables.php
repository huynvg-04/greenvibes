<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutSettingsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên: COD, VNPAY, MOMO'); 
            $table->string('code')->unique()->comment('Mã định danh: cod, vnpay'); 
            $table->string('description')->nullable()->comment('Mô tả: Thanh toán khi nhận hàng...'); // 
            $table->string('image')->nullable()->comment(' Logo phương thức');
            $table->text('config')->nullable()->comment('Lưu cấu hình JSON (API Key, Secret Key...) nếu cần');
            $table->boolean('is_active')->default(true); 
            $table->integer('sort_order')->default(0); 
            $table->timestamps();
        });

        // 2. Bảng Phí vận chuyển
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên: Giao hàng tiêu chuẩn, Hỏa tốc,...'); // 
            $table->decimal('fee', 15, 2)->default(0); 
            $table->decimal('min_order_value', 15, 2)->default(0);
            $table->integer('estimated_days')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_fee')) {
                $table->decimal('shipping_fee', 15, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->nullable()->after('shipping_fee');
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
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('payment_methods');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_fee', 'shipping_method']);
        });
    }
}
