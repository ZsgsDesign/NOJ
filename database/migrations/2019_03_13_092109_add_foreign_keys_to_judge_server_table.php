<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToJudgeServerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('judge_server', function(Blueprint $table)
		{
			$table->foreign('oid', 'judge_server_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('judge_server', function(Blueprint $table)
		{
			$table->dropForeign('judge_server_oid');
		});
	}

}
