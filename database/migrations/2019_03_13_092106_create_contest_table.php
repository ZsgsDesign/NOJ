<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contest', function(Blueprint $table)
		{
			$table->integer('cid', true);
			$table->integer('gid')->nullable()->index('group_gid');
			$table->string('name')->nullable();
			$table->boolean('verified')->nullable();
			$table->boolean('rated')->nullable();
			$table->boolean('anticheated')->nullable();
			$table->boolean('featured')->nullable();
			$table->text('description', 65535)->nullable();
			$table->integer('rule')->nullable();
			$table->dateTime('begin_time')->nullable();
			$table->dateTime('end_time')->nullable();
			$table->boolean('public')->nullable()->comment('1 all 0 group, determine who can see the contest, either way people who participate in this event could see the contest');
			$table->boolean('registration')->nullable()->comment('1 required 0 no, determine whether people need to register before attend');
			$table->dateTime('registration_due')->nullable()->comment('the deadline to register the contest');
			$table->integer('registant_type')->nullable()->comment('2 all,1 group,0 none');
			$table->integer('froze_length')->nullable()->comment('3600 means froze board in the last hour');
			$table->boolean('status_visibility')->nullable()->comment('2 view all, 1 view only oneself, 0 cannot');
			$table->dateTime('create_time')->nullable();
			$table->integer('audit_status')->comment('1 passed 0 passing -1 denied');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contest');
	}

}
