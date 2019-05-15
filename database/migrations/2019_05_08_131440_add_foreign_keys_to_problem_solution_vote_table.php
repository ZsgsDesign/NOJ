<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProblemSolutionVoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('problem_solution_vote', function(Blueprint $table)
		{
			$table->foreign('uid', 'problem_solution_vote_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('psoid', 'problem_solution_vote_psoid')->references('psoid')->on('problem_solution')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('problem_solution_vote', function(Blueprint $table)
		{
			$table->dropForeign('problem_solution_vote_uid');
			$table->dropForeign('problem_solution_vote_psoid');
		});
	}

}
