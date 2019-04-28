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

Route::group(['prefix' => 'account'], function () {
    Route::get('/', 'AccountController@index')->name('account_index');
    Route::get('/dashboard', 'AccountController@dashboard')->middleware('auth')->name('account_dashboard');
});

Route::group(['prefix' => 'user'], function () {
    Route::redirect('/', '/', 301);
    Route::get('/{uid}', 'UserController@view')->name('user_view');
});

Route::get('/problem', 'ProblemController@index')->middleware('contest_account')->name('problem_index');
Route::get('/problem/{pcode}', 'ProblemController@detail')->middleware('contest_account')->name('problem_detail');
Route::get('/problem/{pcode}/editor', 'ProblemController@editor')->middleware('auth')->name('problem_editor');

Route::get('/status', 'StatusController@index')->middleware('contest_account')->name('status_index');

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

Route::group(['prefix' => 'system'], function () {
    Route::redirect('/', '/system/info', 301);
    Route::get('/info', 'SystemController@info')->name('system_info');
});

Route::group(['namespace' => 'Tool'], function () {
    Route::group(['prefix' => 'tool'], function () {
        Route::redirect('/', '/', 301);
        Route::group(['prefix' => 'pastebin'], function () {
            Route::get('/', 'PastebinController@index')->middleware('auth')->name('tool_pastebin_index');
            Route::get('/create', 'PastebinController@create')->middleware('auth')->name('tool_pastebin_create');
            Route::get('/view/{tpid}', 'PastebinController@view')->name('tool_pastebin_view');
        });
        Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax'], function () {
            Route::post('generate', 'PastebinController@generate')->middleware('auth')->name('tool_ajax_pastebin_generate');
        });
    });
    Route::get('/pb/{tpid}', 'PastebinController@view')->name('tool_pastebin_view_shortlink');
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
    Route::get('downloadCode', 'ProblemController@downloadCode')->middleware('auth');

    Route::post('search', 'SearchController')->name('search');

    Route::group(['prefix' => 'group'], function () {
        Route::post('changeNickName', 'GroupController@changeNickName')->middleware('auth');
        Route::get('generateContestAccount', 'GroupController@generateContestAccount')->middleware('auth');
    });

    Route::group(['prefix' => 'contest'], function () {
        Route::post('fetchClarification', 'ContestController@fetchClarification')->middleware('auth');
        Route::post('requestClarification', 'ContestController@requestClarification')->middleware('auth', 'throttle:1,0.34');
        Route::get('rejudge', 'ContestController@rejudge')->middleware('auth');
    });

    Route::group(['prefix' => 'submission'], function () {
        Route::post('detail', 'SubmissionController@detail');
    });

    Route::group(['prefix' => 'account'], function () {
        Route::post('update_avatar', 'AccountController@updateAvatar')->middleware('auth')->name('account_update_avatar');
    });
});

Auth::routes();
