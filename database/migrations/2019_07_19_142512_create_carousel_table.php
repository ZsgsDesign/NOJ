<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCarouselTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('carousel', function(Blueprint $table)
		{
			$table->integer('caid', true);
			$table->string('image')->nullable();
			$table->string('url')->nullable();
			$table->string('title')->nullable();
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
		Schema::drop('carousel');
	}

}
