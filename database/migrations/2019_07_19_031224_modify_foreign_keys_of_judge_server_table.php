<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyForeignKeysOfJudgeServerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('judge_server', function (Blueprint $table) {
            $table->dropForeign('judge_server_oid');
            $table->foreign('oid', 'judge_server_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('judge_server', function (Blueprint $table) {
            $table->dropForeign('judge_server_oid');
            $table->foreign('oid', 'judge_server_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }
}
