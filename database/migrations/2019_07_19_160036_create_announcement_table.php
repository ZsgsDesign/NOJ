<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAnnouncementTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('announcement', function(Blueprint $table)
		{
			$table->integer('anid', true);
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
		Schema::drop('announcement');
	}

}
