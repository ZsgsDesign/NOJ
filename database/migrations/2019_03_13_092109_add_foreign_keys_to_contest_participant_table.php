<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToContestParticipantTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contest_participant', function(Blueprint $table)
		{
			$table->foreign('cid', 'contest_participant_cid')->references('cid')->on('contest')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('uid', 'contest_participant_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contest_participant', function(Blueprint $table)
		{
			$table->dropForeign('contest_participant_cid');
			$table->dropForeign('contest_participant_uid');
		});
	}

}
