<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReelEarningCriteriaTable extends Migration
{
    public function up()
    {
        Schema::create('reel_earning_criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('minimum_views')->default(1000);
            $table->unsignedInteger('minimum_values')->default(100);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reel_earning_criteria');
    }
}
