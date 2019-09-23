<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDojoPassesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dojo_passes', function(Blueprint $table)
        {
            $table->foreign('dojo_id', 'dojo_passes_dojo_id')->references('id')->on('dojos')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id', 'dojo_passes_user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dojo_passes', function(Blueprint $table)
        {
            $table->dropForeign('dojo_passes_dojo_id');
            $table->dropForeign('dojo_passes_user_id');
        });
    }

}
