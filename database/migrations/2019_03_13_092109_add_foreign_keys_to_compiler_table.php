<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCompilerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('compiler', function(Blueprint $table)
		{
			$table->foreign('oid', 'compiler_oid')->references('oid')->on('oj')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('compiler', function(Blueprint $table)
		{
			$table->dropForeign('compiler_oid');
		});
	}

}
