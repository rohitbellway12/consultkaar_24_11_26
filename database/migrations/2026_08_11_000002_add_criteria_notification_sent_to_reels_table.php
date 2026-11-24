<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCriteriaNotificationSentToReelsTable extends Migration
{
    public function up()
    {
        Schema::table('reels', function (Blueprint $table) {
            $table->boolean('criteria_notification_sent')->default(0)->after('status');
        });
    }

    public function down()
    {
        Schema::table('reels', function (Blueprint $table) {
            $table->dropColumn('criteria_notification_sent');
        });
    }
}
