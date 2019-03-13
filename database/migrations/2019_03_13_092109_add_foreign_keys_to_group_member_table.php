<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupMemberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('group_member', function(Blueprint $table)
		{
			$table->foreign('gid', 'member_gid')->references('gid')->on('group')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('uid', 'member_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('group_member', function(Blueprint $table)
		{
			$table->dropForeign('member_gid');
			$table->dropForeign('member_uid');
		});
	}

}
