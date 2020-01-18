<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreconditionToDojos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dojos', function (Blueprint $table) {
            $table->string('precondition')->nullable()->after('passline');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dojos', function (Blueprint $table) {
            $table->dropColumn(['precondition']);
        });
    }
}
