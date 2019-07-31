<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemDiscussionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_discussion', function (Blueprint $table) {
            $table->integer('pdid', true);
			$table->integer('uid')->nullable()->index('problem_discussion_uid');
			$table->integer('pid')->nullable()->index('problem_discussion_pid');
			$table->string('title')->nullable();
			$table->text('content')->nullable();
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
        Schema::dropIfExists('problem_discussion');
    }
}
