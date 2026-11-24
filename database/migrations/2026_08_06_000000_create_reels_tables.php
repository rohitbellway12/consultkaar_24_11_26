<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReelsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('video');
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('value_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('reel_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reel_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('reel_id')->references('id')->on('reels')->onDelete('cascade');
            $table->index(['reel_id', 'user_id']);
        });

        Schema::create('reel_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reel_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('reel_id')->references('id')->on('reels')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['reel_id', 'user_id']);
        });

        Schema::create('reel_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reel_id');
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->timestamps();

            $table->foreign('reel_id')->references('id')->on('reels')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reel_comments');
        Schema::dropIfExists('reel_values');
        Schema::dropIfExists('reel_views');
        Schema::dropIfExists('reels');
    }
}
