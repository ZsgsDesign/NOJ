<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProblemSolutionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('problem_solution', function(Blueprint $table)
		{
			$table->foreign('pid', 'problem_solution_pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('uid', 'problem_solution_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('problem_solution', function(Blueprint $table)
		{
			$table->dropForeign('problem_solution_pid');
			$table->dropForeign('problem_solution_uid');
		});
	}

}
