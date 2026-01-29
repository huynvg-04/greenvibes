<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountColsToMembershipTiers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->decimal('discount', 5, 2)->default(0)->after('name')
                  ->comment('Phần trăm giảm giá (0-100)');

            $table->integer('usage_limit')->default(0)->after('discount')
                  ->comment('Số lần dùng tối đa (0 = không giới hạn)');
    
            $table->enum('usage_period', ['month', 'year', 'lifetime'])->default('month')->after('usage_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membership_tiers', function (Blueprint $table) {
            $table->dropColumn(['discount', 'usage_limit', 'usage_period']);
        });
    }
}
