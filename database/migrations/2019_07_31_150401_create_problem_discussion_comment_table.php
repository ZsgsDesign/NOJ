<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemDiscussionCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_discussion_comment', function (Blueprint $table) {
            $table->integer('pdcid', true);
			$table->integer('pdid')->nullable()->index('problem_discussion_comment_pdid');
			$table->integer('uid')->nullable()->index('problem_discussion_comment_uid');
			$table->integer('pid')->nullable()->index('problem_discussion_comment_pid');
			$table->text('content')->nullable();
			$table->integer('reply_id')->nullable();
			$table->integer('votes')->nullable()->default(0);
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('problem_discussion_comment');
    }
}
