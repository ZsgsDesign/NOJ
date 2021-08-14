<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingForeignKeysForUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_extra', function(Blueprint $table)
		{
            $table->integer('uid')->change();
            $table->foreign('uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_extra', function(Blueprint $table)
		{
            $table->dropForeign(['uid']);
            $table->bigInteger('uid')->change();
		});
    }
}
