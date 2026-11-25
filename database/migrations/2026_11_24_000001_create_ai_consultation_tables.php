<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiConsultationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Trending questions / FAQs for AI Home screen
        if (!Schema::hasTable('ai_trending_questions')) {
            Schema::create('ai_trending_questions', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('category_id')->unsigned()->nullable();
                $table->text('question');
                $table->tinyInteger('status')->default(1);
                $table->integer('priority')->default(0);
                $table->timestamps();

                $table->index('category_id');
                $table->index('status');
            });
        }

        // 2. AI Chat Sessions (5-minute tracking & summary)
        if (!Schema::hasTable('ai_chat_sessions')) {
            Schema::create('ai_chat_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_uuid', 64)->unique();
                $table->bigInteger('user_id')->unsigned();
                $table->bigInteger('category_id')->unsigned()->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->enum('status', ['active', 'expired', 'completed'])->default('active');
                $table->text('summary')->nullable();
                $table->text('user_concern')->nullable();
                $table->json('recommended_advisor_ids')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('category_id');
                $table->index('status');
                $table->index('expires_at');
            });
        }

        // 3. AI Chat Messages
        if (!Schema::hasTable('ai_chat_messages')) {
            Schema::create('ai_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('session_id')->unsigned();
                $table->bigInteger('user_id')->unsigned();
                $table->enum('sender', ['user', 'ai', 'system'])->default('user');
                $table->text('message');
                $table->integer('tokens_used')->default(0);
                $table->timestamps();

                $table->index('session_id');
                $table->index('user_id');
                $table->foreign('session_id')->references('id')->on('ai_chat_sessions')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_chat_sessions');
        Schema::dropIfExists('ai_trending_questions');
    }
}
