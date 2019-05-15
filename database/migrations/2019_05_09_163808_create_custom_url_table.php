<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomUrlTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('custom_url', function(Blueprint $table)
		{
			$table->integer('cuid', true);
			$table->string('display_name')->nullable();
			$table->string('cucode', 50)->nullable()->unique('custom_url_cucode');
			$table->text('url', 65535)->nullable();
			$table->boolean('newtab')->nullable()->default(1);
			$table->boolean('available')->nullable()->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('custom_url');
	}

}
