<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDojoPassesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dojo_passes', function(Blueprint $table)
        {
            $table->integer('id', true);
            $table->integer('dojo_id')->nullable()->index('dojo_passes_dojo_id');
            $table->integer('user_id')->nullable()->index('dojo_passes_user_id');
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
        Schema::drop('dojo_passes');
    }

}
