<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyForeignKeysOfProblemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('problem', function (Blueprint $table) {
            $table->dropForeign('problem_oid');
            $table->foreign('OJ', 'problem_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('problem', function (Blueprint $table) {
            $table->dropForeign('problem_oid');
            $table->foreign('OJ', 'problem_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }
}
