<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDojoPhasesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dojo_phases', function(Blueprint $table)
        {
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->text('description', 65535)->nullable();
            $table->integer('passline')->nullable()->default(0);
            $table->integer('order')->nullable();
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
        Schema::drop('dojo_phases');
    }

}
