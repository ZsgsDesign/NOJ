<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProblemDialectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem_dialects', function (Blueprint $table) {
            $table->id();
            $table->integer('problem_id');
            $table->foreign('problem_id')->references('pid')->on('problem')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->string('dialect_name')->nullable();
            $table->string('dialect_language')->nullable();
            $table->boolean('is_biblioteca')->default(false);
            $table->string('title')->nullable();
            $table->text('description', 65535)->nullable();
            $table->text('input', 65535)->nullable();
            $table->text('output', 65535)->nullable();
            $table->text('note', 65535)->nullable();
            $table->string('copyright')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('problem_dialects');
    }
}
