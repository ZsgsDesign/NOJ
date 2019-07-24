<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRemoteCodeToContestClarificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contest_clarification', function(Blueprint $table)
		{
			$table->string('remote_code')->nullable();
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
			$table->dropColumn('remote_code');
		});
	}

}
