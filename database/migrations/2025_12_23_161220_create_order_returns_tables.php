<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderReturnsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {

        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); 
            
            $table->string('reason'); 
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            
            $table->string('status')->default('pending'); 
            
            $table->decimal('refund_amount', 15, 2)->default(0); 
            $table->text('admin_note')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users'); 
            
            $table->timestamps();
        });

        Schema::create('order_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_return_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained(); 
            $table->integer('quantity');
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
        Schema::dropIfExists('order_return_items');
        Schema::dropIfExists('order_returns');
    }

}