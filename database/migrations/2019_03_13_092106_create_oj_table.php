<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOjTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('oj', function(Blueprint $table)
		{
			$table->integer('oid', true);
			$table->string('ocode', 50)->nullable()->unique('ocode');
			$table->string('name', 50)->nullable();
			$table->string('home_page')->nullable();
			$table->text('crawer_cli', 65535)->nullable();
			$table->text('submitter_cli', 65535)->nullable();
			$table->string('logo')->nullable();
			$table->boolean('status')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('oj');
	}

}
