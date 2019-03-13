<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJudgeServerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('judge_server', function(Blueprint $table)
		{
			$table->integer('jsid', true);
			$table->string('scode')->nullable();
			$table->string('name')->nullable();
			$table->string('host')->nullable();
			$table->string('port')->nullable();
			$table->string('token')->nullable();
			$table->boolean('available')->nullable();
			$table->integer('oid')->nullable()->index('judge_server_oid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('judge_server');
	}

}
