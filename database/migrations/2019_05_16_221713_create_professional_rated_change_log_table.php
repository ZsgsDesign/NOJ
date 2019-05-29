<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfessionalRatedChangeLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('professional_rated_change_log', function(Blueprint $table)
		{
			$table->integer('prclid')->primary();
			$table->integer('uid')->nullable()->index('professional_rated_change_log_uid');
			$table->integer('cid')->nullable()->index('professional_rated_change_log_cid');
			$table->string('rated')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('professional_rated_change_log');
	}

}
