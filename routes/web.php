<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/home', '/', 301);

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/account', 'MainController@account')->name('account');

Route::get('/problem', 'ProblemController@index')->name('problem_index');
Route::get('/problem/{pcode}', 'ProblemController@detail')->name('problem_detail');
Route::get('/problem/{pcode}/editor', 'ProblemController@editor')->name('problem_editor');

Route::get('/group', 'GroupController@index')->name('group_index');
Route::get('/group/{gcode}', 'GroupController@detail')->name('group_detail');

Route::get('/contest', 'ContestController@index')->name('contest_index');

Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax'], function(){
    Route::post('submitSolution', 'ProblemController@submitSolution');
    Route::post('judgeStatus', 'ProblemController@judgeStatus');
    Route::post('manualJudge', 'ProblemController@manualJudge');
    Route::post('submitHistory', 'ProblemController@submitHistory');
    Route::get('crawler', 'ProblemController@crawler');
});

Auth::routes();
