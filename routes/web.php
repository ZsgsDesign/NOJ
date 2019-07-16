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
Route::redirect('/acmhome/welcome.do', '/', 301);
Route::get('/acmhome/problemdetail.do','MainController@oldRedirect')->name('old.redirect');

Route::get('/', 'MainController@home')->middleware('contest_account')->name('home');

Route::group(['prefix' => 'account'], function () {
    Route::get('/', 'AccountController@index')->name('account_index');
    Route::get('/dashboard', 'AccountController@dashboard')->middleware('auth')->name('account_dashboard');
    Route::get('/settings', 'AccountController@settings')->middleware('auth')->name('account_settings');
});

Route::group(['prefix' => 'oauth', 'namespace' => 'OAuth'], function () {
    Route::get('/github', 'GithubController@redirectTo')->name('oauth_github');
    Route::get('/github/unbind','GithubController@unbind')->name('oauth_github_unbind');
    Route::get('/github/unbind/confirm','GithubController@confirmUnbind')->name('oauth_github_unbind_confirm');
    Route::get('/github/callback', 'GithubController@handleCallback')->name('oauth_github_callback');
});

Route::group(['prefix' => 'user'], function () {
    Route::redirect('/', '/', 301);
    Route::get('/{uid}', 'UserController@view')->name('user_view');
});

Route::group(['prefix' => 'problem'], function () {
    Route::get('/', 'ProblemController@index')->middleware('contest_account')->name('problem_index');
    Route::get('/{pcode}', 'ProblemController@detail')->middleware('contest_account')->name('problem_detail');
    Route::get('/{pcode}/editor', 'ProblemController@editor')->middleware('auth')->name('problem_editor');
    Route::get('/{pcode}/solution', 'ProblemController@solution')->middleware('auth')->name('problem_solution');
});
Route::get('/status', 'StatusController@index')->middleware('contest_account')->name('status_index');

Route::group(['prefix' => 'group'], function () {
    Route::get('/', 'GroupController@index')->middleware('contest_account')->name('group_index');
    Route::get('/create', 'GroupController@create')->middleware('contest_account')->name('group.create');
    Route::get('/{gcode}', 'GroupController@detail')->middleware('auth', 'contest_account')->name('group.detail');
    Route::get('/{gcode}/settings', 'GroupController@settings')->middleware('auth', 'contest_account')->name('group.settings');
    Route::get('/{gcode}/settings/general', 'GroupController@settingsGeneral')->middleware('auth', 'contest_account')->name('group.settings.general');
    Route::get('/{gcode}/settings/return', 'GroupController@settingsReturn')->middleware('auth', 'contest_account')->name('group.settings.return');
    Route::get('/{gcode}/settings/danger', 'GroupController@settingsDanger')->middleware('auth', 'contest_account')->name('group.settings.danger');
    Route::get('/{gcode}/settings/some', 'GroupController@settingsSome')->middleware('auth', 'contest_account')->name('group.settings.some');
});

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

Route::group(['prefix' => 'rank'], function () {
    Route::get('/', 'RankController@index')->name('rank_index');
});

Route::group(['namespace' => 'Tool'], function () {
    Route::group(['prefix' => 'tool'], function () {
        Route::redirect('/', '/', 301);
        Route::group(['prefix' => 'pastebin'], function () {
            Route::redirect('/', '/tool/pastebin/create', 301);
            Route::get('/create', 'PastebinController@create')->middleware('auth')->name('tool_pastebin_create');
            Route::get('/view/{$code}', 'PastebinController@view')->name('tool_pastebin_view');
        });
        Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax'], function () {
            Route::group(['prefix' => 'pastebin'], function () {
                Route::post('generate', 'PastebinController@generate')->middleware('auth')->name('tool_ajax_pastebin_generate');
            });
        });
    });
    Route::get('/pb/{code}', 'PastebinController@view')->name('tool_pastebin_view_shortlink');
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
    Route::post('submitSolutionDiscussion', 'ProblemController@submitSolutionDiscussion')->middleware('auth');
    Route::post('updateSolutionDiscussion', 'ProblemController@updateSolutionDiscussion')->middleware('auth');
    Route::post('deleteSolutionDiscussion', 'ProblemController@deleteSolutionDiscussion')->middleware('auth');
    Route::post('voteSolutionDiscussion', 'ProblemController@voteSolutionDiscussion')->middleware('auth');

    Route::post('search', 'SearchController')->name('search');

    Route::group(['prefix' => 'group'], function () {
        Route::post('changeMemberClearance', 'GroupController@changeMemberClearance')->middleware('auth');
        Route::post('changeGroupImage', 'GroupController@changeGroupImage')->middleware('auth');
        Route::post('changeJoinPolicy', 'GroupController@changeJoinPolicy')->middleware('auth');
        Route::post('changeGroupName', 'GroupController@changeGroupName')->middleware('auth');
        Route::post('changeNickName', 'GroupController@changeNickName')->middleware('auth');
        Route::get('generateContestAccount', 'GroupController@generateContestAccount')->middleware('auth');
        Route::post('approveMember', 'GroupController@approveMember')->middleware('auth');
        Route::post('removeMember', 'GroupController@removeMember')->middleware('auth');
        Route::post('createGroup', 'GroupController@createGroup')->middleware('auth');
        Route::post('inviteMember', 'GroupController@inviteMember')->middleware('auth');
        Route::post('createNotice', 'GroupController@createNotice')->middleware('auth');
    });

    Route::group(['prefix' => 'contest'], function () {
        Route::post('fetchClarification', 'ContestController@fetchClarification')->middleware('auth');
        Route::post('requestClarification', 'ContestController@requestClarification')->middleware('auth', 'throttle:1,0.34');
        Route::get('rejudge', 'ContestController@rejudge')->middleware('auth');
        Route::get('updateProfessionalRate', 'ContestController@updateProfessionalRate')->middleware('auth');
        Route::post('registContest', 'ContestController@registContest')->middleware('auth')->name('ajax.contest.registContest');
    });

    Route::group(['prefix' => 'submission'], function () {
        Route::post('detail', 'SubmissionController@detail');
        Route::post('share', 'SubmissionController@share');
    });

    Route::group(['prefix' => 'account'], function () {
        Route::post('update_avatar', 'AccountController@updateAvatar')->middleware('auth')->name('account_update_avatar');
        Route::post('change_basic_info', 'AccountController@changeBasicInfo')->middleware('auth')->name('account_change_basic_info');
        Route::post('change_extra_info', 'AccountController@changeExtraInfo')->middleware('auth')->name('account_change_extra_info');
        Route::post('change_password', 'AccountController@changePassword')->middleware('auth')->name('account_change_password');
        Route::post('check_email_cooldown', 'AccountController@checkEmailCooldown')->middleware('auth')->name('account_check_email_cooldown');
        Route::post('save_editor_width', 'AccountController@saveEditorWidth')->middleware('auth')->name('account_save_editor_width');
    });
});

Auth::routes(['verify' => true]);
