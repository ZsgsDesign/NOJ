<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        if (config('app.multidomain') && !app()->runningInConsole()) {
            config(['app.url' => request()->root()]);
            config(['filesystems.disks.public.url' => request()->root().'/storage']);
            config(['filesystems.disks.NOJPublic.url' => request()->root()]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
