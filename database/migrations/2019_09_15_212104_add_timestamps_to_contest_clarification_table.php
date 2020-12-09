<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToContestClarificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest_clarification', function (Blueprint $table) {
            $table->renameColumn('create_time', 'created_at');
            $table->timestamp('updated_at')->nullable();
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
        Schema::table('contest_clarification', function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->renameColumn('created_at','create_time');
            $table->dropSoftDeletes();
        });
    }
}
