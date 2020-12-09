<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToPastebinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pastebin', function (Blueprint $table) {
            $table->renameColumn('create_date', 'created_at');
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
        Schema::table('pastebin', function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->renameColumn('created_at','create_date');
            $table->dropSoftDeletes();
        });
    }
}
