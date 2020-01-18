<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDojosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dojos', function(Blueprint $table)
        {
            $table->foreign('dojo_phase_id', 'dojos_dojo_phase_id')->references('id')->on('dojo_phases')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dojos', function(Blueprint $table)
        {
            $table->dropForeign('dojos_dojo_phase_id');
        });
    }

}
