<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyForeignKeysOfJudgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('judger', function (Blueprint $table) {
            $table->dropForeign('judger_oid');
            $table->foreign('oid', 'judger_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('judger', function (Blueprint $table) {
            $table->dropForeign('judger_oid');
            $table->foreign('oid', 'judger_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }
}
