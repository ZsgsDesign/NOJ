<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJudgerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('judger', function(Blueprint $table)
		{
			$table->integer('jid', true);
			$table->integer('oid')->nullable()->index('judger_oid');
			$table->string('handle')->nullable();
			$table->string('password')->nullable();
			$table->boolean('available')->nullable();
			$table->boolean('using')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('judger');
	}

}
