<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('sender');
            $table->integer('receiver');
            $table->string('title');
            $table->text('content');
            $table->integer('reply')->nullable();
            $table->tinyInteger('allow_reply')->default(0);
            $table->tinyInteger('unread')->default(1);
            $table->tinyInteger('official')->default(0);
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
        Schema::dropIfExists('message');
    }
}
