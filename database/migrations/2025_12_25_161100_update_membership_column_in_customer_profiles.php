<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMembershipColumnInCustomerProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
           
            $table->dropColumn('level');

            $table->unsignedBigInteger('membership_tier_id')->nullable()->after('user_id');

            $table->foreign('membership_tier_id')
                ->references('id')
                ->on('membership_tiers')
                ->onDelete('set null'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropForeign(['membership_tier_id']);
            $table->dropColumn('membership_tier_id');
            $table->string('level')->nullable();
        });
    }
}
