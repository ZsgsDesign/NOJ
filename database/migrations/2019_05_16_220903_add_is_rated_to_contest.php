<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsRatedToContest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest', function (Blueprint $table) {
            $table->boolean('is_rated')->nullable()->default(0)->after('rated');
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
            $table->dropColumn(['is_rated']);
        });
    }
}
