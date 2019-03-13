<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProblemTagTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('problem_tag', function(Blueprint $table)
		{
			$table->foreign('pid', 'tag_pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('problem_tag', function(Blueprint $table)
		{
			$table->dropForeign('tag_pid');
		});
	}

}
