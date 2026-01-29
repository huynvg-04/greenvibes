<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); 
            $table->string('description')->nullable();
            $table->enum('type', ['fixed', 'percent'])->default('fixed'); 
            $table->decimal('value', 15, 2);
            
        
            $table->enum('scope', ['global', 'specific'])->default('global');
            
            $table->integer('usage_limit')->nullable(); 
            $table->integer('used_count')->default(0); 
            $table->decimal('min_order_value', 15, 2)->default(0);
            
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });

      
        Schema::create('couponables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
         
            $table->morphs('couponable'); 
            
            $table->unique(['coupon_id', 'couponable_id', 'couponable_type'], 'coupon_unique_rel');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists('couponables');
        Schema::dropIfExists('coupons');
    }
}


