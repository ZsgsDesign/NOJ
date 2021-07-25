<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingForeignKeysForGroupAndAbuse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abuses', function(Blueprint $table)
		{
            $table->integer('id')->change();
		});
        Schema::table('group_rated_change_log', function(Blueprint $table)
		{
            $table->foreign('gid')->references('gid')->on('group')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('cid')->references('cid')->on('contest')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
        Schema::table('group_problem_tag', function(Blueprint $table)
		{
            $table->foreign('gid')->references('gid')->on('group')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
        Schema::table('group_banneds', function(Blueprint $table)
		{
            $table->foreign('group_id')->references('gid')->on('group')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('abuse_id')->references('id')->on('abuses')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
        Schema::table('user_banneds', function(Blueprint $table)
		{
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('abuse_id')->references('id')->on('abuses')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_rated_change_log', function(Blueprint $table)
		{
			$table->dropForeign(['gid']);
			$table->dropForeign(['cid']);
			$table->dropForeign(['uid']);
		});
        Schema::table('group_problem_tag', function(Blueprint $table)
		{
			$table->dropForeign(['gid']);
			$table->dropForeign(['pid']);
		});
        Schema::table('group_banneds', function(Blueprint $table)
		{
			$table->dropForeign(['group_id']);
			$table->dropForeign(['abuse_id']);
		});
        Schema::table('user_banneds', function(Blueprint $table)
		{
			$table->dropForeign(['user_id']);
			$table->dropForeign(['abuse_id']);
		});
        Schema::table('abuses', function(Blueprint $table)
		{
            $table->unsignedBigInteger('id')->change();
		});
    }
}
