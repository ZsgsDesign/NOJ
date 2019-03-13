<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProblemTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('problem', function(Blueprint $table)
		{
			$table->integer('pid', true);
			$table->string('pcode', 100)->nullable()->unique('pcode');
			$table->integer('solved_count')->nullable();
			$table->integer('difficulty')->nullable();
			$table->boolean('file')->nullable();
			$table->integer('time_limit')->nullable();
			$table->integer('memory_limit')->nullable();
			$table->string('contest_id')->nullable();
			$table->string('index_id')->nullable();
			$table->string('title')->nullable();
			$table->text('description', 65535)->nullable();
			$table->text('input', 65535)->nullable();
			$table->text('output', 65535)->nullable();
			$table->text('note', 65535)->nullable();
			$table->string('input_type', 20)->nullable();
			$table->string('output_type', 20)->nullable();
			$table->string('origin')->nullable();
			$table->string('source')->nullable();
			$table->integer('OJ')->nullable()->index('problem_oid');
			$table->dateTime('update_date')->nullable();
			$table->integer('tot_score')->nullable()->default(1);
			$table->boolean('partial')->nullable()->default(0);
			$table->boolean('markdown')->nullable()->default(0);
			$table->string('special_compiler')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('problem');
	}

}
