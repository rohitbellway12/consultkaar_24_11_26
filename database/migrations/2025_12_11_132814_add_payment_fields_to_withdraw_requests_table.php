<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToWithdrawRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawl_requests', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->string('receipt_upload')->nullable()->after('payment_method');
            $table->text('rejection_reason')->nullable()->after('receipt_upload');
            $table->string('account_details')->nullable()->after('receipt_upload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdrawl_requests', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'receipt_upload', 'rejection_reason', 'account_details']);
        });
    }
}