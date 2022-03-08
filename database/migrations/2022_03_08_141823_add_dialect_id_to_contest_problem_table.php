<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDialectIdToContestProblemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest_problem', function (Blueprint $table) {
            $table->foreignId('problem_dialect_id')->nullable()->after('pid')->constrained()->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contest_problem', function (Blueprint $table) {
            $table->dropForeign(['problem_dialect_id']);
            $table->dropColumn(['problem_dialect_id']);
        });
    }
}
