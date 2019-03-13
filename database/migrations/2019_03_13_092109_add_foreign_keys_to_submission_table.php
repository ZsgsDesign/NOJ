<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSubmissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('submission', function(Blueprint $table)
		{
			$table->foreign('cid', 'submission_cid')->references('cid')->on('contest')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('coid', 'submission_coid')->references('coid')->on('compiler')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('jid', 'submission_jid')->references('jid')->on('judger')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('pid', 'submission_pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('uid', 'submission_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('submission', function(Blueprint $table)
		{
			$table->dropForeign('submission_cid');
			$table->dropForeign('submission_coid');
			$table->dropForeign('submission_jid');
			$table->dropForeign('submission_pid');
			$table->dropForeign('submission_uid');
		});
	}

}
