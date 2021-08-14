<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingForeignKeysForContest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest_problem', function(Blueprint $table)
		{
            $table->foreign('cid')->references('cid')->on('contest')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contest_problem', function(Blueprint $table)
		{
			$table->dropForeign(['cid']);
			$table->dropForeign(['pid']);
		});
    }
}
