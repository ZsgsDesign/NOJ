<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupHomeworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_homework', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('group_id')->nullable();
            $table->foreign('group_id')->references('gid')->on('group')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_simple')->default(1);
            $table->timestamp('ended_at')->nullable();
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
        Schema::dropIfExists('group_homework');
    }
}
