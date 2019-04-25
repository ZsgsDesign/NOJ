<?php

use Illuminate\Database\Seeder;
use Encore\Admin\Auth\Administrator;
use Encore\Admin\Auth\Role;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Auth\Menu;

class AdminDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create a user.
        Administrator::truncate();
        Administrator::create([
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'name'     => 'Administrator',
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name' => 'Administrator',
            'slug' => 'administrator',
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        //create a permission
        Permission::truncate();
        Permission::insert([
            [
                'name'        => 'All permission',
                'slug'        => '*',
                'http_method' => '',
                'http_path'   => '*',
            ],
            [
                'name'        => 'Dashboard',
                'slug'        => 'dashboard',
                'http_method' => 'GET',
                'http_path'   => '/',
            ],
            [
                'name'        => 'Login',
                'slug'        => 'auth.login',
                'http_method' => '',
                'http_path'   => "/auth/login\r\n/auth/logout",
            ],
            [
                'name'        => 'User setting',
                'slug'        => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path'   => '/auth/setting',
            ],
            [
                'name'        => 'Auth management',
                'slug'        => 'auth.management',
                'http_method' => '',
                'http_path'   => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
        ]);

        Role::first()->permissions()->save(Permission::first());

        // add default menus.
        Menu::truncate();
        Menu::insert([
            [
                'parent_id' => 0,
                'order'     => 1,
                'title'     => 'Index',
                'icon'      => 'fa-bar-chart',
                'uri'       => '/',
            ],
            [
                'parent_id' => 0,
                'order'     => 2,
                'title'     => 'Admin',
                'icon'      => 'fa-tasks',
                'uri'       => '',
            ],
            [
                'parent_id' => 2,
                'order'     => 3,
                'title'     => 'Users',
                'icon'      => 'fa-users',
                'uri'       => 'auth/users',
            ],
            [
                'parent_id' => 2,
                'order'     => 4,
                'title'     => 'Roles',
                'icon'      => 'fa-user',
                'uri'       => 'auth/roles',
            ],
            [
                'parent_id' => 2,
                'order'     => 5,
                'title'     => 'Permission',
                'icon'      => 'fa-ban',
                'uri'       => 'auth/permissions',
            ],
            [
                'parent_id' => 2,
                'order'     => 6,
                'title'     => 'Menu',
                'icon'      => 'fa-bars',
                'uri'       => 'auth/menu',
            ],
            [
                'parent_id' => 2,
                'order'     => 7,
                'title'     => 'Operation log',
                'icon'      => 'fa-history',
                'uri'       => 'auth/logs',
            ],
            [
                'parent_id' => 0,
                'order'     => 8,
                'title'     => 'Users',
                'icon'      => 'fa-user-md',
                'uri'       => '/users',
            ],
            [
                'parent_id' => 0,
                'order'     => 9,
                'title'     => 'Problems',
                'icon'      => 'fa-book',
                'uri'       => '/problems',
            ],
            [
                'parent_id' => 0,
                'order'     => 10,
                'title'     => 'Submissions',
                'icon'      => 'fa-bookmark-o',
                'uri'       => '/submissions',
            ],
            [
                'parent_id' => 0,
                'order'     => 11,
                'title'     => 'Contests',
                'icon'      => 'fa-trophy',
                'uri'       => '/contests',
            ],
            [
                'parent_id' => 0,
                'order'     => 12,
                'title'     => 'Groups',
                'icon'      => 'fa-group',
                'uri'       => '/groups',
            ],
            [
                'parent_id' => 0,
                'order' => 13,
                'title' => 'Helpers',
                'icon' => 'fa-gears',
                'uri' => '',
            ],
            [
                'parent_id' => 13,
                'order' => 14,
                'title' => 'Scaffold',
                'icon' => 'fa-keyboard-o',
                'uri' => 'helpers/scaffold',
            ],
            [
                'parent_id' => 13,
                'order' => 15,
                'title' => 'Database terminal',
                'icon' => 'fa-database',
                'uri' => 'helpers/terminal/database',
            ],
            [
                'parent_id' => 13,
                'order' => 16,
                'title' => 'Laravel artisan',
                'icon' => 'fa-terminal',
                'uri' => 'helpers/terminal/artisan',
            ],
            [
                'parent_id' => 13,
                'order' => 17,
                'title' => 'Routes',
                'icon' => 'fa-list-alt',
                'uri' => 'helpers/routes',
            ],
            [
                'parent_id' => 0,
                'order' => 18,
                'title' => 'Log viewer',
                'icon' => 'fa-database',
                'uri' => 'logs',
            ],
            [
                'parent_id' => 0,
                'order' => 19,
                'title' => 'Api tester',
                'icon' => 'fa-sliders',
                'uri' => 'api-tester',
            ],
            [
                'parent_id' => 0,
                'order' => 20,
                'title' => 'Media manager',
                'icon' => 'fa-file',
                'uri' => 'media',
            ],
            [
                'parent_id' => 0,
                'order' => 21,
                'title' => 'Scheduling',
                'icon' => 'fa-clock-o',
                'uri' => 'scheduling',
            ],
            [
                'parent_id' => 0,
                'order' => 22,
                'title' => 'Backup',
                'icon' => 'fa-copy',
                'uri' => 'backup',
            ],
            [
                'parent_id' => 0,
                'order' => 23,
                'title' => 'Redis manager',
                'icon' => 'fa-database',
                'uri' => 'redis',
            ],
        ]);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
    }
}
