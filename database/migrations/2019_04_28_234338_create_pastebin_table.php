<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePastebinTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pastebin', function(Blueprint $table)
        {
            $table->integer('pbid', true);
            $table->string('lang', 50)->nullable();
            $table->string('title')->nullable()->default('Untitled');
            $table->integer('uid')->nullable()->index('pastebin_uid');
            $table->dateTime('expire')->nullable();
            $table->text('content', 16777215)->nullable();
            $table->string('code', 10)->nullable()->unique('code');
            $table->dateTime('create_date')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pastebin');
    }

}
