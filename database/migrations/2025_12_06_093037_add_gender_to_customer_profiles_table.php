<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderToCustomerProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'other'])->default('other')->after('full_name');
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
            $table->dropColumn('gender');
        });
    }
}
