<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveOutdatedFieldsFromOj extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oj', function (Blueprint $table) {
            $table->dropColumn(['crawer_cli','submitter_cli']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oj', function (Blueprint $table) {
            $table->text('crawer_cli', 65535)->nullable();
			$table->text('submitter_cli', 65535)->nullable();
        });
    }
}
