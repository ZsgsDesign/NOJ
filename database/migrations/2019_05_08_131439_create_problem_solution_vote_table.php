<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProblemSolutionVoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('problem_solution_vote', function(Blueprint $table)
		{
			$table->integer('psovid', true);
			$table->integer('uid')->nullable()->index('problem_solution_vote_uid');
			$table->integer('psoid')->nullable()->index('problem_solution_vote_psoid');
			$table->boolean('type')->nullable()->comment('0 downvote 1 upvote');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('problem_solution_vote');
	}

}
