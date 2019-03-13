<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group', function(Blueprint $table)
		{
			$table->integer('gid', true);
			$table->string('gcode', 50)->nullable()->unique('gcode');
			$table->string('img')->nullable();
			$table->string('name')->nullable();
			$table->boolean('public')->nullable();
			$table->boolean('verified')->nullable();
			$table->text('description', 65535)->nullable();
			$table->integer('join_policy')->nullable();
			$table->string('custom_icon')->nullable();
			$table->string('custom_title')->nullable();
			$table->dateTime('create_time')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group');
	}

}
