<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function(Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.index');
    $router->resource('users', UserController::class);
    $router->resource('announcements', AnnouncementController::class);
    $router->get('problems/import', 'ProblemController@import');
    $router->resource('problems', ProblemController::class);
    $router->resource('solutions', SolutionController::class);
    $router->resource('submissions', SubmissionController::class);
    $router->resource('contests', ContestController::class);
    $router->resource('groups', GroupController::class);
    $router->resource('dojos', DojoController::class);
    $router->resource('dojophases', DojoPhaseController::class);
    $router->resource('dojopasses', DojoPassesController::class);
    $router->resource('judge-server', JudgeServerController::class);
    $router->resource('judger', JudgerController::class);
    $router->resource('abuses', AbuseController::class);
    $router->resource('carousels', CarouselController::class);

    Route::match(['GET', 'POST'], 'codetester', 'CodeTesterController@tester')->name('admin.codetester.tester');
    Route::match(['GET', 'POST'], 'settings', 'SettingsController@index')->name('admin.settings.index');

    Route::group(['prefix' => 'api'], function(Router $router) {
        $router->get('/problems', 'ApiController@problems')->name('admin.api.problems');
        $router->get('/users', 'ApiController@users')->name('admin.api.users');
    });

    Route::group(['prefix' => 'babel'], function(Router $router) {
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
