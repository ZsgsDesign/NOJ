<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameUidAndExpireToPastebinTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pastebin', function (Blueprint $table) {
            $table->renameColumn('expire', 'expired_at');
            $table->renameColumn('uid', 'user_id');

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
            $table->renameColumn('expired_at', 'expire');
            $table->renameColumn('user_id', 'uid');
        });
    }
}
