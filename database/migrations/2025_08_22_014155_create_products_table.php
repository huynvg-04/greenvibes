<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->integer('quantity')->default(0);
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('promotion_id')->nullable();
                $table->decimal('promotion_price', 10, 2)->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('products')) {
            Schema::dropIfExists('products');
        }
    }
};
