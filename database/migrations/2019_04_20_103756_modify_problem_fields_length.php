<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyProblemFieldsLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('problem', function (Blueprint $table) {
            $table->longText('description')->comment(' ')->change();
            $table->mediumText('input')->comment(' ')->change();
            $table->mediumText('output')->comment(' ')->change();
            $table->mediumText('note')->comment(' ')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('problem', function (Blueprint $table) {
            $table->text('description')->comment('')->change();
            $table->text('input')->comment('')->change();
            $table->text('output')->comment('')->change();
            $table->text('note')->comment('')->change();
        });
    }
}
