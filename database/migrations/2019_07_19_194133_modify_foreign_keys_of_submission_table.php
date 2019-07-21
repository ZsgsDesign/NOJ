<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyForeignKeysOfSubmissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submission', function (Blueprint $table) {
            $table->dropForeign('submission_cid');
			$table->dropForeign('submission_coid');
			$table->dropForeign('submission_jid');
			$table->dropForeign('submission_pid');
            $table->dropForeign('submission_uid');
            $table->foreign('cid', 'submission_cid')->references('cid')->on('contest')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('coid', 'submission_coid')->references('coid')->on('compiler')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('jid', 'submission_jid')->references('jid')->on('judger')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('pid', 'submission_pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('uid', 'submission_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submission', function (Blueprint $table) {
            $table->dropForeign('submission_cid');
			$table->dropForeign('submission_coid');
			$table->dropForeign('submission_jid');
			$table->dropForeign('submission_pid');
            $table->dropForeign('submission_uid');
            $table->foreign('cid', 'submission_cid')->references('cid')->on('contest')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('coid', 'submission_coid')->references('coid')->on('compiler')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('jid', 'submission_jid')->references('jid')->on('judger')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('pid', 'submission_pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('uid', 'submission_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }
}
