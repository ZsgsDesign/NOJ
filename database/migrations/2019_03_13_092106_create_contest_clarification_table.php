<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestClarificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest_clarification', function(Blueprint $table)
		{
			$table->integer('ccid', true);
			$table->boolean('type')->nullable()->comment('0 announcement 1 clarification');
			$table->integer('cid')->nullable()->index('contest_clarification_cid');
			$table->string('title')->nullable();
			$table->text('content', 65535)->nullable();
			$table->text('relpy', 65535)->nullable();
			$table->dateTime('create_time')->nullable();
			$table->boolean('public')->nullable()->comment('1 all 0 nobody');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest_clarification');
	}

}
