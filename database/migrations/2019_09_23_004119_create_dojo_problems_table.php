<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDojoProblemsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dojo_problems', function(Blueprint $table)
        {
            $table->integer('id', true);
            $table->integer('dojo_id')->nullable()->index('dojo_id');
            $table->integer('problem_id')->nullable()->index('problem_id');
            $table->integer('order')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dojo_problems');
    }

}
