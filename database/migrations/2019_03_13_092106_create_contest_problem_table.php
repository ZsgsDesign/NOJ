<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestProblemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest_problem', function(Blueprint $table)
		{
			$table->integer('cpid', true);
			$table->integer('cid')->nullable();
			$table->integer('number')->nullable();
			$table->string('ncode', 5)->nullable();
			$table->integer('pid')->nullable();
			$table->string('alias')->nullable();
			$table->integer('points')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest_problem');
	}

}
