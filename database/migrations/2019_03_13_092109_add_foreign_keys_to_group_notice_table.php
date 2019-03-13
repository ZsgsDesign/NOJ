<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupNoticeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('group_notice', function(Blueprint $table)
		{
			$table->foreign('gid', 'group_notice_gid')->references('gid')->on('group')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('uid', 'group_notice_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('group_notice', function(Blueprint $table)
		{
			$table->dropForeign('group_notice_gid');
			$table->dropForeign('group_notice_uid');
		});
	}

}
