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

Route::get('/', 'MainController@home')->middleware('contest_account')->name('home');

Route::get('/search', 'SearchController')->middleware('auth')->name('search');

Route::group(['prefix' => 'message','as' => 'message.','middleware' => ['user.banned','auth']], function () {
    Route::get('/', 'MessageController@index')->name('index');
    Route::get('/{id}', 'MessageController@detail')->name('detail');
});

Route::group(['prefix' => 'account','middleware' => ['user.banned','auth']], function () {
    Route::get('/', 'AccountController@index')->name('account.index');
    Route::get('/dashboard', 'AccountController@dashboard')->name('account.dashboard');
    Route::get('/settings', 'AccountController@settings')->name('account.settings');
});

Route::group(['prefix' => 'oauth', 'namespace' => 'OAuth', 'as' => 'oauth.', 'middleware' => ['user.banned']], function () {
    Route::group(['prefix' => 'github', 'as' => 'github.'], function () {
        Route::get('/', 'GithubController@redirectTo')->name('index');
        Route::get('/unbind','GithubController@unbind')->name('unbind');
        Route::get('/unbind/confirm','GithubController@confirmUnbind')->name('unbind.confirm');
        Route::get('/callback', 'GithubController@handleCallback')->name('callback');
    });
    Route::group(['prefix' => 'aauth', 'as' => 'aauth.'], function () {
        Route::get('/', 'AAuthController@redirectTo')->name('index');
        Route::get('/unbind','AAuthController@unbind')->name('unbind');
        Route::get('/unbind/confirm','AAuthController@confirmUnbind')->name('unbind.confirm');
        Route::get('/callback', 'AAuthController@handleCallback')->name('callback');
    });
});

Route::group(['prefix' => 'user','as' => 'user.', 'middleware' => ['user.banned','auth','contest_account']], function () {
    Route::redirect('/', '/', 301);
    Route::get('/{uid}', 'UserController@view')->name('view');
});

Route::group(['prefix' => 'problem', 'middleware' => ['user.banned', 'contest_account']], function () {
    Route::get('/', 'ProblemController@index')->name('problem.index');
    Route::get('/{pcode}', 'ProblemController@detail')->name('problem.detail');
    Route::get('/{pcode}/editor', 'ProblemController@editor')->middleware('auth')->name('problem.editor');
    Route::get('/{pcode}/solution', 'ProblemController@solution')->middleware('auth')->name('problem.solution');
    Route::get('/{pcode}/discussion', 'ProblemController@discussion')->middleware('auth')->name('problem.discussion');
});

Route::get('/discussion/{dcode}', 'ProblemController@discussionPost')->middleware('auth', 'contest_account', 'user.banned')->name('problem.discussion.post');

Route::get('/status', 'StatusController@index')->middleware('contest_account', 'user.banned')->name('status.index');

Route::group(['prefix' => 'dojo','as' => 'dojo.','middleware' => ['user.banned', 'contest_account']], function () {
    Route::get('/', 'DojoController@index')->name('index');
});

Route::group(['namespace' => 'Group', 'prefix' => 'group','as' => 'group.','middleware' => ['contest_account', 'user.banned']], function () {
    Route::get('/', 'IndexController@index')->name('index');
    Route::get('/create', 'IndexController@create')->name('create');
    Route::get('/{gcode}', 'IndexController@detail')->middleware('auth', 'group.exist', 'group.banned')->name('detail');

    Route::get('/{gcode}/analysis', 'IndexController@analysis')->middleware('auth', 'group.exist', 'group.banned')->name('analysis');
    Route::get('/{gcode}/homework', 'IndexController@allHomework')->middleware('auth', 'group.exist', 'group.banned')->name('allHomework');
    Route::get('/{gcode}/homework/{homework_id}', 'IndexController@homework')->middleware('auth', 'group.exist', 'group.banned')->name('homework');
    Route::get('/{gcode}/homework/{homework_id}/statistics', 'IndexController@homeworkStatistics')->middleware('auth', 'group.exist', 'group.banned')->name('homeworkStatistics');
    Route::get('/{gcode}/analysisDownload', 'IndexController@analysisDownload')->middleware('auth', 'group.exist', 'group.banned')->name('analysis.download');
    Route::group(['prefix' => '{gcode}/settings', 'as' => 'settings.', 'middleware' => ['privileged', 'group.exist', 'group.banned']], function () {
        Route::get('/', 'AdminController@settings')->middleware('auth')->name('index');
        Route::get('/general', 'AdminController@settingsGeneral')->middleware('auth')->name('general');
        Route::get('/return', 'AdminController@settingsReturn')->middleware('auth')->name('return');
        Route::get('/danger', 'AdminController@settingsDanger')->middleware('auth')->name('danger');
        Route::get('/member', 'AdminController@settingsMember')->middleware('auth')->name('member');
        Route::get('/contest', 'AdminController@settingsContest')->middleware('auth')->name('contest');
        Route::get('/problems', 'AdminController@problems')->middleware('auth')->name('problems');
        Route::get('/homework', 'AdminController@homework')->middleware('auth')->name('homework');
    });
});

Route::group([ 'namespace' => 'Contest', 'prefix' => 'contest', 'as' => 'contest.', 'middleware' => [ 'contest_account', 'user.banned' ] ], function () {

    Route::get('/', 'IndexController@index')->name('index');

    Route::group(['prefix' => '{cid}', 'middleware' => ['contest.exists']], function () {

        Route::get('/', 'IndexController@detail')->name('detail');

        Route::group(['as' => 'board.', 'prefix' => 'board', 'middleware' => ['auth']], function () {

            Route::group(['middleware' => ['contest.desktop']], function () {
                Route::get('/', 'BoardController@board')->name('index');
                Route::get('/challenge', 'BoardController@challenge')->name('challenge');
                Route::get('/challenge/{ncode}', 'BoardController@editor')->name('editor');
                Route::get('/rank', 'BoardController@rank')->name('rank');
                Route::get('/status', 'BoardController@status')->name('status');
                Route::get('/clarification', 'BoardController@clarification')->name('clarification');
                Route::get('/print', 'BoardController@print')->name('print');
                Route::get('/analysis', 'BoardController@analysis')->name('analysis');
            });

            Route::group(['prefix' => 'admin'], function () {
                Route::get('/', 'AdminController@admin')->middleware(['privileged'])->name('admin');
                Route::get('/scrollBoard', 'AdminController@scrollBoard')->middleware(['contest_account', 'privileged'])->name('admin.scrollboard');
                Route::get('/downloadContestAccountXlsx', 'AdminController@downloadContestAccountXlsx')->name('admin.download.contestaccountxlsx');
                Route::get('/refreshContestRank', 'AdminController@refreshContestRank')->name('admin.refresh.contestrank');
                Route::get('/pdfView', 'AdminController@pdfView')->middleware(['contest.board.admin.pdfview.clearance'])->name('admin.pdf.view');
            });

        });

    });

});

Route::group(['prefix' => 'system', 'middleware' => ['user.banned']], function () {
    Route::redirect('/', '/system/info', 301);
    Route::get('/info', 'SystemController@info')->name('system_info');
});

Route::group(['prefix' => 'rank', 'middleware' => ['user.banned']], function () {
    Route::get('/', 'RankController@index')->middleware('contest_account')->name('rank_index');
});

Route::group(['prefix' => 'terms', 'middleware' => ['user.banned']], function () {
    Route::redirect('/', '/terms/user', 301);
    Route::get('/user', 'TermsController@user')->name('terms.user');
});

Route::group(['namespace' => 'Tool', 'middleware' => ['contest_account', 'user.banned']], function () {
    Route::group(['prefix' => 'tool'], function () {
        Route::redirect('/', '/', 301);
        Route::group(['prefix' => 'pastebin'], function () {
            Route::redirect('/', '/tool/pastebin/create', 301);
            Route::get('/create', 'PastebinController@create')->middleware('auth')->name('tool.pastebin.create');
            Route::get('/view/{code}', 'PastebinController@view')->name('tool.pastebin.view');
        });
        Route::group(['prefix' => 'imagehosting'], function () {
            Route::redirect('/', '/tool/imagehosting/create', 301);
            Route::get('/create', 'ImageHostingController@create')->middleware('auth')->name('tool.imagehosting.create');
            Route::get('/list', 'ImageHostingController@list')->middleware('auth')->name('tool.imagehosting.list');
            Route::redirect('/detail', '/tool/imagehosting/list', 301);
            Route::get('/detail/{id}', 'ImageHostingController@detail')->middleware('auth')->name('tool.imagehosting.detail');
        });
        Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax'], function () {
            Route::group(['prefix' => 'pastebin'], function () {
                Route::post('generate', 'PastebinController@generate')->middleware('auth')->name('tool.ajax.pastebin.generate');
            });
            Route::group(['prefix' => 'imagehosting'], function () {
                Route::post('generate', 'ImageHostingController@generate')->middleware('auth')->name('tool.ajax.imagehosting.generate');
            });
        });
    });
    Route::get('/pb/{code}', 'PastebinController@view')->name('tool.pastebin.view.shortlink');
});

Route::group(['prefix' => 'ajax', 'namespace' => 'Ajax', 'middleware' => ['user.banned']], function () {
    Route::post('submitSolution', 'ProblemController@submitSolution')->middleware('auth', 'throttle:1,0.17');
    Route::post('resubmitSolution', 'ProblemController@resubmitSolution')->middleware('auth', 'throttle:1,0.17');
    Route::post('judgeStatus', 'ProblemController@judgeStatus')->middleware('auth');
    Route::post('manualJudge', 'ProblemController@manualJudge')->middleware('auth');
    Route::post('submitHistory', 'ProblemController@submitHistory')->middleware('auth');
    Route::post('problemExists', 'ProblemController@problemExists')->middleware('auth')->name('ajax.problemExists');
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
        Route::post('createHomework', 'GroupManageController@createHomework')->middleware('auth')->name('ajax.group.createHomework');

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

        Route::post('rejudge', 'ContestAdminController@rejudge')->middleware('auth')->name('ajax.contest.rejudge');
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
        Route::post('removePDF', 'ContestAdminController@removePDF')->middleware('auth')->name('ajax.contest.removePDF');
        Route::post('anticheat', 'ContestAdminController@anticheat')->middleware('auth')->name('ajax.contest.anticheat');
        Route::get('downloadPlagiarismReport', 'ContestAdminController@downloadPlagiarismReport')->middleware('auth')->name('ajax.contest.downloadPlagiarismReport');
    });

    Route::group(['prefix' => 'submission'], function () {
        Route::post('detail', 'SubmissionController@detail');
        Route::post('share', 'SubmissionController@share');
    });

    Route::group(['prefix' => 'account'], function () {
        Route::post('updateAvatar', 'AccountController@updateAvatar')->middleware('auth')->name('ajax.account.update.avatar');
        Route::post('changeBasicInfo', 'AccountController@changeBasicInfo')->middleware('auth')->name('ajax.account.change.basicinfo');
        Route::post('changeExtraInfo', 'AccountController@changeExtraInfo')->middleware('auth')->name('ajax.account.change.extrainfo');
        Route::post('changePassword', 'AccountController@changePassword')->middleware('auth')->name('ajax.account.change.password');
        Route::post('checkEmailCooldown', 'AccountController@checkEmailCooldown')->middleware('auth')->name('ajax.account.check.emailcooldown');
        Route::post('saveEditorWidth', 'AccountController@saveEditorWidth')->middleware('auth')->name('ajax.account.save.editorwidth');
        Route::post('saveEditorTheme', 'AccountController@saveEditorTheme')->middleware('auth')->name('ajax.account.save.editortheme');
    });

    Route::group(['prefix' => 'abuse'], function () {
        Route::post('report', 'AbuseController@report')->middleware('auth')->name('ajax.abuse.report');
    });

    Route::group(['prefix' => 'dojo'], function () {
        Route::post('dojo', 'DojoController@complete')->middleware('auth')->name('ajax.dojo.complete');
    });
});

if(config("function.register")){
    Auth::routes(['verify' => true]);
} else {
    Auth::routes(['verify' => true, 'register' => false]);
}
