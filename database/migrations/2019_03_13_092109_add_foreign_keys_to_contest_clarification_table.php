<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToContestClarificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contest_clarification', function(Blueprint $table)
		{
			$table->foreign('cid', 'contest_clarification_cid')->references('cid')->on('contest')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contest_clarification', function(Blueprint $table)
		{
			$table->dropForeign('contest_clarification_cid');
		});
	}

}
