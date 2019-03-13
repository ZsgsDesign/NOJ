<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupMemberTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_member', function(Blueprint $table)
		{
			$table->integer('gmid', true);
			$table->integer('gid')->nullable()->index('member_gid');
			$table->integer('uid')->nullable()->index('member_uid');
			$table->integer('role')->nullable()->comment('3 leader
2 manager
1 member
0 applicant
-1 invited
-3 not joined');
			$table->string('nick_name')->nullable();
			$table->string('sub_group')->nullable();
			$table->dateTime('join_time')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_member');
	}

}
