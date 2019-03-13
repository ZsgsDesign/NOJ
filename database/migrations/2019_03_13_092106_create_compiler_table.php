<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompilerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('compiler', function(Blueprint $table)
		{
			$table->integer('coid', true);
			$table->integer('oid')->nullable()->index('compiler_oid');
			$table->string('comp')->nullable();
			$table->string('lang')->nullable();
			$table->string('lcode')->nullable();
			$table->string('icon')->nullable();
			$table->string('display_name')->nullable();
			$table->string('available')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('compiler');
	}

}
