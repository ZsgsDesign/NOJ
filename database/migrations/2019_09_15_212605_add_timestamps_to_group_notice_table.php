<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsToGroupNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_notice', function (Blueprint $table) {
            $table->renameColumn('post_date', 'created_at');
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
        Schema::table('group_notice', function (Blueprint $table) {
            $table->dropColumn('updated_at');
            $table->renameColumn('created_at','post_date');
            $table->dropSoftDeletes();
        });
    }
}
