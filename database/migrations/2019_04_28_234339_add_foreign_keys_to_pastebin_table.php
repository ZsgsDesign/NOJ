<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPastebinTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pastebin', function(Blueprint $table)
        {
            $table->foreign('uid', 'pastebin_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pastebin', function(Blueprint $table)
        {
            $table->dropForeign('pastebin_uid');
        });
    }

}
