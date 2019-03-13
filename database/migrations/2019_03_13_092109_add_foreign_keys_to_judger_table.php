<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToJudgerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('judger', function(Blueprint $table)
		{
			$table->foreign('oid', 'judger_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('judger', function(Blueprint $table)
		{
			$table->dropForeign('judger_oid');
		});
	}

}
