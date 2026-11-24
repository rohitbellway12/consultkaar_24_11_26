<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'domain_id')) {
                $table->unsignedBigInteger('domain_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'identity_proofs')) {
                $table->text('identity_proofs')->nullable()->comment('JSON of identity proofs');
            }
            if (!Schema::hasColumn('users', 'relevant_documents')) {
                $table->text('relevant_documents')->nullable()->comment('JSON of relevant documents');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['domain_id', 'identity_proofs', 'relevant_documents']);
        });
    }
};
