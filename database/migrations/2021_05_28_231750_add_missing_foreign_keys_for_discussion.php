<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingForeignKeysForDiscussion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('problem_discussion', function(Blueprint $table)
		{
            $table->foreign('uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
        Schema::table('problem_discussion_comment', function(Blueprint $table)
		{
            $table->foreign('pdid')->references('pdid')->on('problem_discussion')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('pid')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('problem_discussion', function(Blueprint $table)
		{
            $table->dropForeign(['uid']);
            $table->dropForeign(['pid']);
		});
        Schema::table('problem_discussion_comment', function(Blueprint $table)
		{
            $table->dropForeign(['pdid']);
            $table->dropForeign(['uid']);
            $table->dropForeign(['pid']);
		});
    }
}
