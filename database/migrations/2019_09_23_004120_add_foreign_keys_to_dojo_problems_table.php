<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDojoProblemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dojo_problems', function(Blueprint $table)
        {
            $table->foreign('dojo_id', 'dojo_id')->references('id')->on('dojos')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('problem_id', 'problem_id')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dojo_problems', function(Blueprint $table)
        {
            $table->dropForeign('dojo_id');
            $table->dropForeign('problem_id');
        });
    }

}
