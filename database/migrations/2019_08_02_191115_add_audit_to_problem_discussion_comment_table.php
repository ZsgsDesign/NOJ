<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuditToProblemDiscussionCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('problem_discussion_comment', function (Blueprint $table) {
            $table->integer('audit')->nullable()->default("1");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('problem_discussion_comment', function (Blueprint $table) {
            $table->dropColumn(['audit']);
        });
    }
}
