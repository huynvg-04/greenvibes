<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReviewsAndCreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('reviews', 'images')) {
                $table->json('images')->nullable()->after('comment');
            }
            if (!Schema::hasColumn('reviews', 'likes_count')) {
                $table->integer('likes_count')->default(0)->after('images');
            }
        });

        Schema::create('review_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['review_id', 'user_id']);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('review_likes');
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['images', 'likes_count']);
        });
    }
}
