<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLevelDatesToCustomerProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            // level đã có sẵn như bạn nói
            $table->decimal('total_spent_lifetime', 15, 2)->default(0); 
            $table->integer('total_orders_lifetime')->default(0); 
            $table->timestamp('level_updated_at')->nullable();
            $table->timestamp('level_expires_at')->nullable();
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
            $table->dropColumn(['total_spent_lifetime', 'total_orders_lifetime', 'level_updated_at', 'level_expires_at']);
        });
    }
}
