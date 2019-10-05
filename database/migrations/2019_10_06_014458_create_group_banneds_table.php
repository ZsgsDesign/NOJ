<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupBannedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_banneds', function (Blueprint $table) {
            $table->integer('id',true);
            $table->integer('group_id');
            $table->integer('abuse_id')->nullable();
            $table->string('reason')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('group_banneds');
    }
}
