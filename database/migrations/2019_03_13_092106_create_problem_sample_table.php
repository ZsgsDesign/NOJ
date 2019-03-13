<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProblemSampleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('problem_sample', function(Blueprint $table)
		{
			$table->integer('psid', true);
			$table->integer('pid')->nullable()->index('pid');
			$table->text('sample_input', 65535)->nullable();
			$table->text('sample_output', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('problem_sample');
	}

}
