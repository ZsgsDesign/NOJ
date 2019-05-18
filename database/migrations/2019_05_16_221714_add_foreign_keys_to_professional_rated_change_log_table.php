<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProfessionalRatedChangeLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('professional_rated_change_log', function(Blueprint $table)
		{
			$table->foreign('uid', 'professional_rated_change_log_uid')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('cid', 'professional_rated_change_log_cid')->references('cid')->on('contest')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('professional_rated_change_log', function(Blueprint $table)
		{
			$table->dropForeign('professional_rated_change_log_uid');
			$table->dropForeign('professional_rated_change_log_cid');
		});
	}

}
