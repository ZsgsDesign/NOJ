<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupTagTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_tag', function(Blueprint $table)
		{
			$table->integer('gtid', true);
			$table->string('tag')->nullable();
			$table->integer('gid')->nullable()->index('tag_gid');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_tag');
	}

}
