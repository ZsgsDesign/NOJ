<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProblemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('problem', function(Blueprint $table)
		{
			$table->foreign('OJ', 'problem_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('problem', function(Blueprint $table)
		{
			$table->dropForeign('problem_oid');
		});
	}

}
