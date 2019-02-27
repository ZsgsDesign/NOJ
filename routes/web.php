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

Route::get('/', 'MainController@home')->name('home');

Route::get('/account', 'MainController@account')->name('account');

Route::get('/problem', 'ProblemController@index')->name('problem_index');
Route::get('/problem/{pcode}', 'ProblemController@detail')->name('problem_detail');
Route::get('/problem/{pcode}/editor', 'ProblemController@editor')->middleware('auth')->name('problem_editor');

Route::get('/group', 'GroupController@index')->name('group_index');
Route::get('/group/{gcode}', 'GroupController@detail')->middleware('auth')->name('group_detail');

Route::get('/contest', 'ContestController@index')->name('contest_index');
Route::get('/contest/{cid}', 'ContestController@detail')->name('contest_detail');
Route::get('/contest/{cid}/board', 'ContestController@board')->middleware('auth')->name('contest_board');
Route::get('/contest/{cid}/board/challenge', 'ContestController@challenge')->middleware('auth')->name('contest_challenge');
Route::get('/contest/{cid}/board/challenge/{ncode}', 'ContestController@editor')->middleware('auth')->name('contest_editor');
Route::get('/contest/{cid}/board/rank', 'ContestController@rank')->middleware('auth')->name('contest_rank');
Route::get('/contest/{cid}/board/clarification', 'ContestController@clarification')->middleware('auth')->name('contest_clarification');
Route::get('/contest/{cid}/board/print', 'ContestController@print')->middleware('auth')->name('contest_print');

Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax'], function () {
    Route::post('submitSolution', 'ProblemController@submitSolution')->middleware('auth');
    Route::post('judgeStatus', 'ProblemController@judgeStatus')->middleware('auth');
    Route::post('manualJudge', 'ProblemController@manualJudge')->middleware('auth');
    Route::post('submitHistory', 'ProblemController@submitHistory')->middleware('auth');
    Route::get('crawler', 'ProblemController@crawler')->middleware('auth');
    Route::post('problemExists', 'ProblemController@problemExists')->middleware('auth');
    Route::post('arrangeContest', 'GroupController@arrangeContest')->middleware('auth');
});

Auth::routes();
