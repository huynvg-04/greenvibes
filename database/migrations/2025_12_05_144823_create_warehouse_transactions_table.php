<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     Schema::create('warehouse_transactions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('product_variant_id')
                  ->constrained('product_variants')
                  ->onDelete('cascade');

            $table->enum('type', ['in', 'out'])->comment('in: Nhập, out: Xuất');
            
            $table->integer('quantity');
            
            $table->integer('balance_after')->nullable()->comment('Số lượng còn lại sau giao dịch');

            $table->nullableMorphs('reference');

            $table->foreignId('user_id')->nullable()->constrained('users');

            $table->string('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_transactions');
    }
}
