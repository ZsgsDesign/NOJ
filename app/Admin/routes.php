<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function(Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('users', UserController::class);
    $router->get('problems/import', 'ProblemController@import');
    $router->resource('problems', ProblemController::class);
    $router->resource('solutions', SolutionController::class);
    $router->resource('submissions', SubmissionController::class);
    $router->resource('contests', ContestController::class);
    $router->resource('groups', GroupController::class);
    $router->resource('abuses', AbuseController::class);

    Route::group(['prefix' => 'babel'], function (Router $router) {
        $router->get('/', 'BabelController@index')->name('admin.babel.index');
        $router->get('installed', 'BabelController@installed')->name('admin.babel.installed');
        $router->get('marketspace', 'BabelController@marketspace')->name('admin.babel.marketspace');
        $router->get('marketspace/{code}', 'BabelController@detail')->name('admin.babel.detail');
        $router->get('update/{extension}', 'BabelController@update')->name('admin.babel.update');
        $router->post('update/{extension}', 'BabelController@updateExtension')->name('admin.babel.updateExtension');
        $router->get('install/{extension}', 'BabelController@install')->name('admin.babel.install');
        $router->post('install/{extension}', 'BabelController@installExtension')->name('admin.babel.installExtension');
    });

});
