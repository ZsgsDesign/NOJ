<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestJudgerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest_judger', function(Blueprint $table)
		{
			$table->integer('jid', true);
			$table->integer('oid')->nullable()->index('oid');
			$table->integer('vcid')->nullable();
			$table->string('handle')->nullable();
			$table->string('password')->nullable();
			$table->boolean('available')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest_judger');
	}

}
