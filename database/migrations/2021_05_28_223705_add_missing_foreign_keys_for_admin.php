<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingForeignKeysForAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_menu', function(Blueprint $table)
		{
            $table->integer('id')->change();
		});
        Schema::table('admin_operation_log', function(Blueprint $table)
		{
            $table->integer('id')->change();
		});
        Schema::table('admin_permissions', function(Blueprint $table)
		{
            $table->integer('id')->change();
		});
        Schema::table('admin_roles', function(Blueprint $table)
		{
            $table->integer('id')->change();
		});
        Schema::table('admin_users', function(Blueprint $table)
		{
            $table->integer('id')->change();
		});

        // Schema::table('admin_role_menu', function(Blueprint $table)
		// {
        //     $table->foreign('role_id')->references('id')->on('admin_roles')->onUpdate('CASCADE')->onDelete('CASCADE');
        //     $table->foreign('menu_id')->references('id')->on('admin_menu')->onUpdate('CASCADE')->onDelete('CASCADE');
		// });
        // Schema::table('admin_operation_log', function(Blueprint $table)
		// {
        //     $table->foreign('user_id')->references('id')->on('admin_users')->onUpdate('CASCADE')->onDelete('CASCADE');
		// });
        // Schema::table('admin_role_permissions', function(Blueprint $table)
		// {
        //     $table->foreign('permission_id')->references('id')->on('admin_permissions')->onUpdate('CASCADE')->onDelete('CASCADE');
        //     $table->foreign('role_id')->references('id')->on('admin_roles')->onUpdate('CASCADE')->onDelete('CASCADE');
		// });
        // Schema::table('admin_role_users', function(Blueprint $table)
		// {
        //     $table->foreign('role_id')->references('id')->on('admin_roles')->onUpdate('CASCADE')->onDelete('CASCADE');
        //     $table->foreign('user_id')->references('id')->on('admin_users')->onUpdate('CASCADE')->onDelete('CASCADE');
		// });
        // Schema::table('admin_user_permissions', function(Blueprint $table)
		// {
        //     $table->foreign('permission_id')->references('id')->on('admin_permissions')->onUpdate('CASCADE')->onDelete('CASCADE');
        //     $table->foreign('user_id')->references('id')->on('admin_users')->onUpdate('CASCADE')->onDelete('CASCADE');
		// });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('admin_role_menu', function(Blueprint $table)
		// {
		// 	$table->dropForeign(['role_id']);
		// 	$table->dropForeign(['menu_id']);
		// });
        // Schema::table('admin_operation_log', function(Blueprint $table)
		// {
        //     $table->dropForeign(['user_id']);
		// });
        // Schema::table('admin_role_permissions', function(Blueprint $table)
		// {
        //     $table->dropForeign(['permission_id']);
        //     $table->dropForeign(['role_id']);
		// });
        // Schema::table('admin_role_users', function(Blueprint $table)
		// {
        //     $table->dropForeign(['user_id']);
        //     $table->dropForeign(['role_id']);
		// });
        // Schema::table('admin_user_permissions', function(Blueprint $table)
		// {
        //     $table->dropForeign(['permission_id']);
        //     $table->dropForeign(['user_id']);
		// });

        Schema::table('admin_menu', function(Blueprint $table)
		{
            $table->unsignedInteger('id')->change();
		});
        Schema::table('admin_operation_log', function(Blueprint $table)
		{
            $table->unsignedInteger('id')->change();
		});
        Schema::table('admin_permissions', function(Blueprint $table)
		{
            $table->unsignedInteger('id')->change();
		});
        Schema::table('admin_roles', function(Blueprint $table)
		{
            $table->unsignedInteger('id')->change();
		});
        Schema::table('admin_users', function(Blueprint $table)
		{
            $table->unsignedInteger('id')->change();
		});
    }
}
