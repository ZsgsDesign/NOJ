<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupNoticeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_notice', function(Blueprint $table)
		{
			$table->integer('gnid', true);
			$table->integer('gid')->nullable()->unique('group_notice_gid');
			$table->integer('uid')->nullable()->index('group_notice_uid');
			$table->string('title')->nullable();
			$table->text('content', 65535)->nullable();
			$table->dateTime('post_date')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_notice');
	}

}
