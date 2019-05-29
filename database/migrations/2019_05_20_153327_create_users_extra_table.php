<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersExtraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_extra', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('uid');
            $table->tinyInteger('key');
            $table->string('value');
            $table->tinyInteger('secret_level')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_extra');
    }
}
