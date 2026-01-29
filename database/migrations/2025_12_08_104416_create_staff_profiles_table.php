<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('position')->nullable(); // chức vụ
            $table->decimal('salary', 15, 2)->default(0); // lương
            $table->date('start_date')->nullable(); // ngày vào làm
            $table->enum('status', ['active', 'quit', 'maternity'])->default('active'); // Đang làm, Nghỉ việc, Thai sản

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
        Schema::dropIfExists('staff_profiles');
    }
}
