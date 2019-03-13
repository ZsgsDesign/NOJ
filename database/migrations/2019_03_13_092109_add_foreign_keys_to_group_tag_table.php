<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupTagTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('group_tag', function(Blueprint $table)
		{
			$table->foreign('gid', 'tag_gid')->references('gid')->on('group')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('group_tag', function(Blueprint $table)
		{
			$table->dropForeign('tag_gid');
		});
	}

}
