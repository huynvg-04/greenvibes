<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'status')) {
                $table->tinyInteger('status')
                    ->default(1)
                    ->after('description')
                    ->comment('1: Active (Đang bán), 0: Inactive (Ẩn/Ngừng bán)');
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
        Schema::table('products', function (Blueprint $table) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'status')) {
                    $table->dropColumn('status');
                }
            });
        });
    }
}
