<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameReplyInContestClarification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest_clarification', function (Blueprint $table) {
            $table->renameColumn('relpy', 'reply');
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
            $table->renameColumn('reply', 'relpy');
        });
    }
}
