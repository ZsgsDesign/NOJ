<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProblemSampleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('problem_sample', function(Blueprint $table)
		{
			$table->foreign('pid', 'sample_pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('problem_sample', function(Blueprint $table)
		{
			$table->dropForeign('sample_pid');
		});
	}

}
