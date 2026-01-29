<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            $table->string('facebook_id')->nullable();
            $table->string('google_id')->nullable();

            $table->enum('level', ['bronze', 'silver', 'gold', 'platinum', 'diamond'])->default('bronze');
            $table->enum('status', ['active', 'blocked'])->default('active');

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
        Schema::dropIfExists('customer_profiles');
    }
}
