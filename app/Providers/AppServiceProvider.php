<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        if(config('app.multidomain')) {
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
