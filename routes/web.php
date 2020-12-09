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

use App\Models\Eloquent\Group;


Route::redirect('/home', '/', 301);
Route::redirect('/acmhome/welcome.do', '/', 301);
Route::get('/acmhome/problemdetail.do','MainController@oldRedirect')->name('old.redirect');
Route::get('/opensearch.xml', function () {
    return response(getOpenSearchXML(), 200)->header("Content-type","text/xml");
});
Route::group(['as' => 'latex.'], function () {
    Route::get('/latex.svg','LatexController@svg')->name('svg');
    Route::get('/latex.png','LatexController@png')->name('png');
});

Route::get('/', 'MainController@home')->middleware('contest_account')->name('home');

Route::get('/search', 'SearchController')->middleware('auth')->name('search');

Route::group(['prefix' => 'message','as' => 'message.','middleware' => ['user.banned','auth']], function () {
    Route::get('/', 'MessageController@index')->name('index');
    Route::get('/{id}', 'MessageController@detail')->name('detail');
});

Route::group(['prefix' => 'account','middleware' => ['user.banned','auth']], function () {
    Route::get('/', 'AccountController@index')->name('account_index');
    Route::get('/dashboard', 'AccountController@dashboard')->name('account_dashboard');
    Route::get('/settings', 'AccountController@settings')->name('account_settings');
});

Route::group(['prefix' => 'oauth', 'namespace' => 'OAuth', 'as' => 'oauth.', 'middleware' => ['user.banned','auth']], function () {
    Route::group(['prefix' => 'github', 'as' => 'github.'], function () {
        Route::get('/', 'GithubController@redirectTo')->name('index');
        Route::get('/unbind','GithubController@unbind')->name('unbind');
        Route::get('/unbind/confirm','GithubController@confirmUnbind')->name('unbind.confirm');
        Route::get('/callback', 'GithubController@handleCallback')->name('callback');
    });
});

Route::group(['prefix' => 'user','as' => 'user.', 'middleware' => ['user.banned','auth','contest_account']], function () {
    Route::redirect('/', '/', 301);
    Route::get('/{uid}', 'UserController@view')->name('view');
});

Route::group(['prefix' => 'problem', 'middleware' => ['user.banned', 'contest_account']], function () {
    Route::get('/', 'ProblemController@index')->name('problem_index');
    Route::get('/{pcode}', 'ProblemController@detail')->name('problem.detail');
    Route::get('/{pcode}/editor', 'ProblemController@editor')->middleware('auth')->name('problem_editor');
    Route::get('/{pcode}/solution', 'ProblemController@solution')->middleware('auth')->name('problem_solution');
    Route::get('/{pcode}/discussion', 'ProblemController@discussion')->middleware('auth')->name('problem.discussion');
});

Route::get('/discussion/{dcode}', 'ProblemController@discussionPost')->middleware('auth', 'contest_account', 'user.banned')->name('problem.discussion.post');

Route::get('/status', 'StatusController@index')->middleware('contest_account', 'user.banned')->name('status_index');

Route::group(['prefix' => 'dojo','as' => 'dojo.','middleware' => ['user.banned', 'contest_account']], function () {
    Route::get('/', 'DojoController@index')->name('index');
});

Route::group(['namespace' => 'Group', 'prefix' => 'group','as' => 'group.','middleware' => ['contest_account', 'user.banned']], function () {
    Route::get('/', 'IndexController@index')->name('index');
    Route::get('/create', 'IndexController@create')->name('create');
    Route::get('/{gcode}', 'IndexController@detail')->middleware('auth', 'group.exist', 'group.banned')->name('detail');

    Route::get('/{gcode}/analysis', 'IndexController@analysis')->middleware('auth', 'group.exist', 'group.banned')->name('analysis');
    Route::get('/{gcode}/analysisDownload', 'IndexController@analysisDownload')->middleware('auth', 'group.exist', 'group.banned')->name('analysis.download');
    Route::group(['prefix' => '{gcode}/settings','as' => 'settings.', 'middleware' => ['privileged', 'group.exist', 'group.banned']], function () {
        Route::get('/', 'AdminController@settings')->middleware('auth')->name('index');
        Route::get('/general', 'AdminController@settingsGeneral')->middleware('auth')->name('general');
        Route::get('/return', 'AdminController@settingsReturn')->middleware('auth')->name('return');
        Route::get('/danger', 'AdminController@settingsDanger')->middleware('auth')->name('danger');
        Route::get('/member', 'AdminController@settingsMember')->middleware('auth')->name('member');
        Route::get('/contest', 'AdminController@settingsContest')->middleware('auth')->name('contest');
        Route::get('/problems', 'AdminController@problems')->middleware('auth')->name('problems');
    });
});

Route::group([
    'namespace' => 'Contest',
    'prefix' => 'contest',
    'as' => 'contest.',
    'middleware' => [
        'contest_account',
        'user.banned'
    ]
], function () {
    Route::get('/', 'IndexController@index')->name('index');
    Route::get('/{cid}', 'IndexController@detail')->name('detail');

    Route::group(['middleware' => ['contest.desktop']], function () {
        Route::get('/{cid}/board', 'BoardController@board')->middleware('auth')->name('board');
        Route::get('/{cid}/board/challenge', 'BoardController@challenge')->middleware('auth')->name('challenge');
        Route::get('/{cid}/board/challenge/{ncode}', 'BoardController@editor')->middleware('auth')->name('editor');
        Route::get('/{cid}/board/rank', 'BoardController@rank')->middleware('auth')->name('rank');
        Route::get('/{cid}/board/status', 'BoardController@status')->middleware('auth')->name('status');
        Route::get('/{cid}/board/clarification', 'BoardController@clarification')->middleware('auth')->name('clarification');
        Route::get('/{cid}/board/print', 'BoardController@print')->middleware('auth')->name('print');
        Route::get('/{cid}/board/analysis', 'BoardController@analysis')->middleware('auth')->name('analysis');
    });

    Route::get('/{cid}/scrollBoard', 'AdminController@scrollBoard')->middleware('auth', 'contest_account', 'privileged')->name('scrollboard');
    Route::get('/{cid}/board/admin', 'AdminController@admin')->middleware('auth', 'privileged')->name('admin');
    Route::get('/{cid}/admin/downloadContestAccountXlsx', 'AdminController@downloadContestAccountXlsx')->middleware('auth')->name('downloadContestAccountXlsx');
    Route::get('/{cid}/admin/refreshContestRank', 'AdminController@refreshContestRank')->middleware('auth')->name('refreshContestRank');
});

Route::group(['prefix' => 'system', 'middleware' => ['user.banned']], function () {
    Route::redirect('/', '/system/info', 301);
    Route::get('/info', 'SystemController@info')->name('system_info');
});

Route::group(['prefix' => 'rank', 'middleware' => ['user.banned']], function () {
    Route::get('/', 'RankController@index')->middleware('contest_account')->name('rank_index');
});

Route::group(['prefix' => 'term', 'middleware' => ['user.banned']], function () {
    Route::redirect('/', '/term/user', 301);
    Route::get('/user', 'TermController@user')->name('term.user');
});

Route::group(['namespace' => 'Tool', 'middleware' => ['contest_account', 'user.banned']], function () {
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

Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax', 'middleware' => ['user.banned']], function () {
    Route::post('submitSolution', 'ProblemController@submitSolution')->middleware('auth', 'throttle:1,0.17');
    Route::post('resubmitSolution', 'ProblemController@resubmitSolution')->middleware('auth', 'throttle:1,0.17');
    Route::post('judgeStatus', 'ProblemController@judgeStatus')->middleware('auth');
    Route::post('manualJudge', 'ProblemController@manualJudge')->middleware('auth');
    Route::post('submitHistory', 'ProblemController@submitHistory')->middleware('auth');
    Route::post('problemExists', 'ProblemController@problemExists')->middleware('auth');
    Route::post('arrangeContest', 'GroupManageController@arrangeContest')->middleware('auth');
    Route::post('joinGroup', 'GroupController@joinGroup')->middleware('auth');
    Route::post('exitGroup', 'GroupController@exitGroup')->middleware('auth');
    Route::get('downloadCode', 'ProblemController@downloadCode')->middleware('auth');
    Route::post('submitSolutionDiscussion', 'ProblemController@submitSolutionDiscussion')->middleware('auth');
    Route::post('updateSolutionDiscussion', 'ProblemController@updateSolutionDiscussion')->middleware('auth');
    Route::post('deleteSolutionDiscussion', 'ProblemController@deleteSolutionDiscussion')->middleware('auth');
    Route::post('voteSolutionDiscussion', 'ProblemController@voteSolutionDiscussion')->middleware('auth');
    Route::post('postDiscussion', 'ProblemController@postDiscussion')->middleware('auth');
    Route::post('addComment', 'ProblemController@addComment')->middleware('auth');

    Route::post('search', 'SearchController')->middleware('auth')->name('ajax.search');

    Route::group(['prefix' => 'message'], function () {
        Route::post('unread', 'MessageController@unread')->middleware('auth');
        Route::post('allRead', 'MessageController@allRead')->middleware('auth');
        Route::post('allDelete', 'MessageController@deleteAll')->middleware('auth');
    });

    Route::group(['prefix' => 'group'], function () {
        Route::post('changeNickName', 'GroupController@changeNickName')->middleware('auth');
        Route::post('createGroup', 'GroupController@createGroup')->middleware('auth');
        Route::post('getPracticeStat', 'GroupController@getPracticeStat')->middleware('auth');
        Route::post('eloChangeLog', 'GroupController@eloChangeLog')->middleware('auth');

        Route::post('changeMemberClearance', 'GroupManageController@changeMemberClearance')->middleware('auth');
        Route::post('changeGroupImage', 'GroupManageController@changeGroupImage')->middleware('auth');
        Route::post('changeJoinPolicy', 'GroupManageController@changeJoinPolicy')->middleware('auth');
        Route::post('changeGroupName', 'GroupManageController@changeGroupName')->middleware('auth');
        Route::post('approveMember', 'GroupManageController@approveMember')->middleware('auth');
        Route::post('removeMember', 'GroupManageController@removeMember')->middleware('auth');
        Route::post('inviteMember', 'GroupManageController@inviteMember')->middleware('auth');
        Route::post('createNotice', 'GroupManageController@createNotice')->middleware('auth');
        Route::post('changeSubGroup', 'GroupManageController@changeSubGroup')->middleware('auth');

        Route::post('addProblemTag', 'GroupAdminController@addProblemTag')->middleware('auth');
        Route::post('removeProblemTag', 'GroupAdminController@removeProblemTag')->middleware('auth');
        Route::get('generateContestAccount', 'GroupAdminController@generateContestAccount')->middleware('auth');
        Route::post('refreshElo', 'GroupAdminController@refreshElo')->middleware('auth');
    });

    Route::group(['prefix' => 'contest'], function () {
        Route::get('updateProfessionalRate', 'ContestController@updateProfessionalRate')->middleware('auth');
        Route::post('fetchClarification', 'ContestController@fetchClarification')->middleware('auth');
        Route::post('requestClarification', 'ContestController@requestClarification')->middleware('auth', 'throttle:1,0.34');
        Route::post('registContest', 'ContestController@registContest')->middleware('auth')->name('ajax.contest.registContest');
        Route::post('getAnalysisData', 'ContestController@getAnalysisData')->middleware('auth')->name('ajax.contest.getAnalysisData');
        Route::get('downloadPDF', 'ContestController@downloadPDF')->middleware('auth')->name('ajax.contest.downloadPDF');

        Route::get('rejudge', 'ContestAdminController@rejudge')->middleware('auth');
        Route::post('details', 'ContestAdminController@details')->middleware('auth');
        Route::post('assignMember', 'ContestAdminController@assignMember')->middleware('auth');
        Route::post('update', 'ContestAdminController@update')->middleware('auth');
        Route::post('issueAnnouncement', 'ContestAdminController@issueAnnouncement')->middleware('auth');
        Route::post('replyClarification', 'ContestAdminController@replyClarification')->middleware('auth');
        Route::post('setClarificationPublic', 'ContestAdminController@setClarificationPublic')->middleware('auth');
        Route::post('generateContestAccount', 'ContestAdminController@generateContestAccount')->middleware('auth');
        Route::post('getScrollBoardData', 'ContestAdminController@getScrollBoardData')->middleware('auth')->name('ajax.contest.getScrollBoardData');
        Route::get('downloadCode', 'ContestAdminController@downloadCode')->middleware('auth');
        Route::post('generatePDF', 'ContestAdminController@generatePDF')->middleware('auth')->name('ajax.contest.generatePDF');
        Route::post('anticheat', 'ContestAdminController@anticheat')->middleware('auth')->name('ajax.contest.anticheat');
        Route::get('downloadPlagiarismReport', 'ContestAdminController@downloadPlagiarismReport')->middleware('auth')->name('ajax.contest.downloadPlagiarismReport');
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

    Route::group(['prefix' => 'abuse'], function () {
        Route::post('report', 'AbuseController@report')->middleware('auth')->name('ajax.abuse.report');
    });

    Route::group(['prefix' => 'dojo'], function () {
        Route::post('dojo', 'DojoController@complete')->middleware('auth')->name('ajax.dojo.complete');
    });
});

Auth::routes(['verify' => true]);
