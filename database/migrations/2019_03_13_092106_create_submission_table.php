<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubmissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('submission', function(Blueprint $table)
		{
			$table->integer('sid', true);
			$table->integer('time')->nullable();
			$table->string('verdict')->nullable();
			$table->string('color')->nullable();
			$table->text('solution', 65535)->nullable();
			$table->string('language')->nullable();
			$table->integer('submission_date')->nullable();
			$table->integer('memory')->nullable();
			$table->string('remote_id', 50)->nullable();
			$table->text('compile_info', 65535)->nullable();
			$table->integer('uid')->nullable()->index('submission_uid');
			$table->integer('cid')->nullable()->index('submission_cid');
			$table->integer('pid')->nullable()->index('submission_pid');
			$table->integer('jid')->nullable()->index('submission_jid');
			$table->integer('coid')->nullable()->index('submission_coid');
			$table->integer('score')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('submission');
	}

}
