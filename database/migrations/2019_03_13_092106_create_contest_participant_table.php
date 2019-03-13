<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestParticipantTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest_participant', function(Blueprint $table)
		{
			$table->integer('cpid', true);
			$table->integer('cid')->nullable()->index('contest_participant_cid');
			$table->integer('uid')->nullable()->index('contest_participant_uid');
			$table->boolean('audit')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest_participant');
	}

}
