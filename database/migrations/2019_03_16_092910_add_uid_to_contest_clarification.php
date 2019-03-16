<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUidToContestClarification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest_clarification', function (Blueprint $table) {
            $table->integer('uid')->nullable();
            $table->foreign('uid', 'contest_clarification_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contest_clarification', function (Blueprint $table) {
            $table->dropForeign('contest_clarification_uid');
            $table->dropColumn(['uid']);
        });
    }
}
