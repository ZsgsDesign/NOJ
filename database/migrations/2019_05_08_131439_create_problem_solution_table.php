<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProblemSolutionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('problem_solution', function(Blueprint $table)
		{
			$table->integer('psoid', true);
			$table->integer('uid')->nullable()->index('problem_solution_uid');
			$table->integer('pid')->nullable()->index('problem_solution_pid');
			$table->text('content')->nullable();
			$table->boolean('audit')->nullable();
			$table->integer('votes')->nullable()->default(0);
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('problem_solution');
	}

}
