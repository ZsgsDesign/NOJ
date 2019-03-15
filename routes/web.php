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

Route::get('/', 'MainController@home')->middleware('contest_account')->name('home');

Route::get('/account', 'MainController@account')->name('account');

Route::get('/problem', 'ProblemController@index')->middleware('contest_account')->name('problem_index');
Route::get('/problem/{pcode}', 'ProblemController@detail')->middleware('contest_account')->name('problem_detail');
Route::get('/problem/{pcode}/editor', 'ProblemController@editor')->middleware('auth')->name('problem_editor');

Route::get('/group', 'GroupController@index')->middleware('contest_account')->name('group_index');
Route::get('/group/{gcode}', 'GroupController@detail')->middleware('auth', 'contest_account')->name('group_detail');

Route::group(['prefix' => 'contest'], function () {
    Route::get('/', 'ContestController@index')->middleware('contest_account')->name('contest_index');
    Route::get('/{cid}', 'ContestController@detail')->middleware('contest_account')->name('contest_detail');
    Route::get('/{cid}/board', 'ContestController@board')->middleware('auth', 'contest_account')->name('contest_board');
    Route::get('/{cid}/board/challenge', 'ContestController@challenge')->middleware('auth', 'contest_account')->name('contest_challenge');
    Route::get('/{cid}/board/challenge/{ncode}', 'ContestController@editor')->middleware('auth', 'contest_account')->name('contest_editor');
    Route::get('/{cid}/board/rank', 'ContestController@rank')->middleware('auth', 'contest_account')->name('contest_rank');
    Route::get('/{cid}/board/status', 'ContestController@status')->middleware('auth', 'contest_account')->name('contest_status');
    Route::get('/{cid}/board/clarification', 'ContestController@clarification')->middleware('auth', 'contest_account')->name('contest_clarification');
    Route::get('/{cid}/board/print', 'ContestController@print')->middleware('auth', 'contest_account')->name('contest_print');
});

Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax'], function () {
    Route::post('submitSolution', 'ProblemController@submitSolution')->middleware('auth', 'throttle:1,0.17');
    Route::post('judgeStatus', 'ProblemController@judgeStatus')->middleware('auth');
    Route::post('manualJudge', 'ProblemController@manualJudge')->middleware('auth');
    Route::post('submitHistory', 'ProblemController@submitHistory')->middleware('auth');
    Route::get('crawler', 'ProblemController@crawler')->middleware('auth');
    Route::post('problemExists', 'ProblemController@problemExists')->middleware('auth');
    Route::post('arrangeContest', 'GroupController@arrangeContest')->middleware('auth');
    Route::post('joinGroup', 'GroupController@joinGroup')->middleware('auth');

    Route::group(['prefix' => 'group'], function () {
        Route::post('changeNickName', 'GroupController@changeNickName')->middleware('auth');
        Route::get('generateContestAccount', 'GroupController@generateContestAccount')->middleware('auth');
    });

    Route::group(['prefix' => 'contest'], function () {
        Route::post('fetchClarification', 'ContestController@fetchClarification')->middleware('auth');
    });
});

Auth::routes();
