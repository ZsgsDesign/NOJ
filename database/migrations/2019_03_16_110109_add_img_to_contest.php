<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImgToContest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest', function (Blueprint $table) {
            $table->string('img')->nullable()->default("/static/img/contest/default.jpg");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contest', function (Blueprint $table) {
            $table->dropColumn(['img']);
        });
    }
}
