<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupHomeworkProblemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_homework_problems', function (Blueprint $table) {
            $table->id();
            $table->integer('group_homework_id')->nullable();
            $table->integer('problem_id')->nullable();
            $table->integer('order_index')->nullable();
            $table->foreign('group_homework_id')->references('id')->on('group_homework')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('problem_id')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('group_homework_problems');
    }
}
