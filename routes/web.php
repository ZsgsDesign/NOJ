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
Route::get('/acmhome/problemdetail.do','MainController@legacyRedirect')->name('old.redirect');
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

Route::group(['prefix' => 'problem', 'as' => 'problem.', 'middleware' => ['user.banned', 'contest_account']], function () {
    Route::get('/', 'ProblemController@index')->name('index');
    Route::group(['prefix' => '{pcode}', 'middleware' => ['problem.valid:pcode']], function () {
        Route::get('/', 'ProblemController@detail')->name('detail');
        Route::group(['middleware' => ['auth']], function () {
            Route::get('/editor', 'ProblemController@editor')->name('editor');
            Route::group(['prefix' => 'discussion', 'as' => 'discussion.'], function () {
                Route::redirect('/', '/problem/{pcode}/discussion/article', 301);
                Route::group(['prefix' => 'solution', 'as' => 'solution.'], function () {
                    Route::get('/', 'ProblemController@solution')->name('index');
                });
                Route::group(['prefix' => 'article', 'as' => 'article.'], function () {
                    Route::get('/', 'ProblemController@discussion')->name('index');
                    Route::get('/{dcode}', 'ProblemController@discussionPost')->name('detail');
                });
            });
        });
    });
});

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
        Route::group(['as' => 'board.', 'prefix' => 'board'], function () {
            Route::group(['middleware' => ['auth', 'contest.desktop']], function () {
                Route::get('/', 'BoardController@board')->name('index');
                Route::get('/challenge', 'BoardController@challenge')->name('challenge');
                Route::get('/challenge/{ncode}', 'BoardController@editor')->middleware(['contest.challenge.exists', 'contest.challenge.problem.exists', 'problem.not_blockaded:cid'])->name('editor');
                Route::get('/rank', 'BoardController@rank')->name('rank');
                Route::get('/status', 'BoardController@status')->name('status');
                Route::get('/clarification', 'BoardController@clarification')->name('clarification');
                Route::get('/print', 'BoardController@print')->name('print');
                Route::get('/analysis', 'BoardController@analysis')->name('analysis');
            });
            Route::group(['prefix' => 'admin'], function () {
                Route::group(['middleware' => ['auth']], function () {
                    Route::get('/', 'AdminController@admin')->middleware(['privileged'])->name('admin');
                    Route::get('/scrollBoard', 'AdminController@scrollBoard')->middleware(['contest_account', 'privileged'])->name('admin.scrollboard');
                    Route::get('/downloadContestAccountXlsx', 'AdminController@downloadContestAccountXlsx')->name('admin.download.contestaccountxlsx');
                    Route::get('/refreshContestRank', 'AdminController@refreshContestRank')->name('admin.refresh.contestrank');
                });
                Route::get('/pdfView', 'AdminController@pdfView')->middleware(['contest.board.admin.pdfview.clearance'])->name('admin.pdf.view');
            });
        });
    });

});

Route::group(['prefix' => 'system', 'middleware' => ['user.banned']], function () {
    Route::redirect('/', '/system/info', 301);
    Route::get('/info', 'SystemController@info')->name('system_info');
});

Route::group(['prefix' => 'rank', 'as' => 'rank.', 'middleware' => ['user.banned']], function () {
    Route::get('/', 'RankController@index')->middleware('contest_account')->name('index');
});

Route::group(['prefix' => 'terms', 'middleware' => ['user.banned']], function () {
    Route::redirect('/', '/terms/user', 301);
    Route::get('/user', 'TermsController@user')->name('terms.user');
});

Route::group(['namespace' => 'Tool', 'as' => 'tool.', 'middleware' => ['contest_account', 'user.banned']], function () {
    Route::get('/pb/{code}', 'PastebinController@view')->name('pastebin.shortlink');
    Route::group(['prefix' => 'tool'], function () {
        Route::redirect('/', '/', 301)->name('index');
        Route::group(['prefix' => 'pastebin', 'as' => 'pastebin.'], function () {
            Route::redirect('/', '/tool/pastebin/create', 301)->name('index');
            Route::get('/create', 'PastebinController@create')->middleware('auth')->name('create');
            Route::get('/view/{code}', 'PastebinController@view')->name('view');
        });
        Route::group(['prefix' => 'imagehosting', 'as' => 'imagehosting.'], function () {
            Route::redirect('/', '/tool/imagehosting/create', 301)->name('index');
            Route::get('/create', 'ImageHostingController@create')->middleware('auth')->name('create');
            Route::get('/list', 'ImageHostingController@list')->middleware('auth')->name('list');
            Route::redirect('/detail', '/tool/imagehosting/list', 301)->name('detail.index');
            Route::get('/detail/{id}', 'ImageHostingController@detail')->middleware('auth')->name('detail');
        });
    });
});

Route::group(['prefix' => 'ajax', 'as' => 'ajax.', 'namespace' => 'Ajax', 'middleware' => ['user.banned']], function () {
    Route::group(['prefix' => 'submission', 'as' => 'submission.'], function () {
        Route::post('detail', 'SubmissionController@detail')->name('detail');
        Route::post('share', 'SubmissionController@share')->name('share');
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::post('search', 'SearchController')->name('search');

        Route::group(['prefix' => 'tool', 'as' => 'tool.', 'namespace' => 'Tool'], function () {
            Route::group(['prefix' => 'pastebin', 'as' => 'pastebin.'], function () {
                Route::post('generate', 'PastebinController@generate')->middleware('auth')->name('generate');
            });
            Route::group(['prefix' => 'imagehosting', 'as' => 'imagehosting.'], function () {
                Route::post('generate', 'ImageHostingController@generate')->middleware('auth')->name('generate');
            });
        });

        Route::post('judgeStatus', 'ProblemController@judgeStatus')->name('judgeStatus');
        Route::post('manualJudge', 'ProblemController@manualJudge')->name('manualJudge');
        Route::post('submitHistory', 'ProblemController@submitHistory')->name('submitHistory');
        Route::get('downloadCode', 'ProblemController@downloadCode')->name('downloadCode');

        Route::group(['prefix' => 'problem', 'as' => 'problem.'], function () {
            Route::get('dialects', 'ProblemController@dialects')->middleware('problem.valid:pid')->name('dialects');
            Route::get('exists', 'ProblemController@exists')->middleware('problem.valid:pcode')->name('exists');
            Route::group(['prefix' => 'solution', 'as' => 'solution.', 'middleware' => ['throttle:1,0.17']], function () {
                Route::post('judge', 'ProblemController@submitSolution')->middleware('problem.valid:pid,contest')->name('judge');
                Route::post('rejudge', 'ProblemController@resubmitSolution')->name('rejudge');
            });
            Route::group(['prefix' => 'discussion', 'as' => 'discussion.', 'middleware' => ['problem.valid:pid']], function () {
                Route::group(['prefix' => 'solution', 'as' => 'solution.'], function () {
                    Route::post('submit', 'ProblemController@submitSolutionDiscussion')->name('submit');
                    Route::post('update', 'ProblemController@updateSolutionDiscussion')->name('update');
                    Route::post('delete', 'ProblemController@deleteSolutionDiscussion')->name('delete');
                    Route::post('vote', 'ProblemController@voteSolutionDiscussion')->name('vote');
                });
                Route::group(['prefix' => 'article', 'as' => 'article.'], function () {
                    Route::post('create', 'ProblemController@postDiscussion')->name('create');
                    Route::post('comment', 'ProblemController@addComment')->name('comment');
                });
            });
        });

        Route::group(['prefix' => 'message', 'as' => 'message.'], function () {
            Route::post('unread', 'MessageController@unread')->name('unread');
            Route::post('allRead', 'MessageController@allRead')->name('allRead');
            Route::post('allDelete', 'MessageController@deleteAll')->name('allDelete');
        });

        Route::group(['prefix' => 'group', 'as' => 'group.'], function () {
            Route::post('arrangeContest', 'GroupManageController@arrangeContest')->name('arrangeContest');
            Route::post('joinGroup', 'GroupController@joinGroup')->name('joinGroup');
            Route::post('exitGroup', 'GroupController@exitGroup')->name('exitGroup');

            Route::post('changeNickName', 'GroupController@changeNickName')->name('changeNickName');
            Route::post('createGroup', 'GroupController@createGroup')->name('createGroup');
            Route::post('getPracticeStat', 'GroupController@getPracticeStat')->name('getPracticeStat');
            Route::post('eloChangeLog', 'GroupController@eloChangeLog')->name('eloChangeLog');

            Route::post('changeMemberClearance', 'GroupManageController@changeMemberClearance')->name('changeMemberClearance');
            Route::post('changeGroupImage', 'GroupManageController@changeGroupImage')->name('changeGroupImage');
            Route::post('changeJoinPolicy', 'GroupManageController@changeJoinPolicy')->name('changeJoinPolicy');
            Route::post('changeGroupName', 'GroupManageController@changeGroupName')->name('changeGroupName');
            Route::post('approveMember', 'GroupManageController@approveMember')->name('approveMember');
            Route::post('removeMember', 'GroupManageController@removeMember')->name('removeMember');
            Route::post('inviteMember', 'GroupManageController@inviteMember')->name('inviteMember');
            Route::post('createNotice', 'GroupManageController@createNotice')->name('createNotice');
            Route::post('changeSubGroup', 'GroupManageController@changeSubGroup')->name('changeSubGroup');
            Route::post('createHomework', 'GroupManageController@createHomework')->name('createHomework');

            Route::post('addProblemTag', 'GroupAdminController@addProblemTag')->name('addProblemTag');
            Route::post('removeProblemTag', 'GroupAdminController@removeProblemTag')->name('removeProblemTag');
            Route::get('generateContestAccount', 'GroupAdminController@generateContestAccount')->name('generateContestAccount');
            Route::post('refreshElo', 'GroupAdminController@refreshElo')->name('refreshElo');
        });

        Route::group(['prefix' => 'contest', 'as' => 'contest.'], function () {
            Route::group(['prefix' => 'clarification', 'as' => 'clarification.'], function () {
                Route::post('fetch', 'ContestController@fetchClarification')->name('fetch');
                Route::post('request', 'ContestController@requestClarification')->middleware('throttle:1,0.34')->name('request');
            });

            Route::get('updateProfessionalRate', 'ContestController@updateProfessionalRate')->name('updateProfessionalRate');
            Route::post('registContest', 'ContestController@registContest')->name('registContest');
            Route::post('getAnalysisData', 'ContestController@getAnalysisData')->name('getAnalysisData');
            Route::get('downloadPDF', 'ContestController@downloadPDF')->name('downloadPDF');

            Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
                Route::group(['prefix' => 'clarification', 'as' => 'clarification.'], function () {
                    Route::post('reply', 'ContestAdminController@replyClarification')->name('reply');
                    Route::post('publicity', 'ContestAdminController@setClarificationPublic')->name('publicity');
                });
                Route::group(['prefix' => 'announcement', 'as' => 'announcement.'], function () {
                    Route::post('issue', 'ContestAdminController@issueAnnouncement')->name('issue');
                });
            });

            Route::post('rejudge', 'ContestAdminController@rejudge')->name('rejudge');
            Route::post('details', 'ContestAdminController@details')->name('details');
            Route::post('assignMember', 'ContestAdminController@assignMember')->name('assignMember');
            Route::post('update', 'ContestAdminController@update')->name('update');
            Route::post('generateContestAccount', 'ContestAdminController@generateContestAccount')->name('generateContestAccount');
            Route::post('getScrollBoardData', 'ContestAdminController@getScrollBoardData')->name('getScrollBoardData');
            Route::get('downloadCode', 'ContestAdminController@downloadCode')->name('downloadCode');
            Route::post('generatePDF', 'ContestAdminController@generatePDF')->name('generatePDF');
            Route::post('removePDF', 'ContestAdminController@removePDF')->name('removePDF');
            Route::post('anticheat', 'ContestAdminController@anticheat')->name('anticheat');
            Route::get('downloadPlagiarismReport', 'ContestAdminController@downloadPlagiarismReport')->name('downloadPlagiarismReport');
        });

        Route::group(['prefix' => 'account', 'as' => 'account.'], function () {
            Route::post('updateAvatar', 'AccountController@updateAvatar')->name('update.avatar');
            Route::post('changeBasicInfo', 'AccountController@changeBasicInfo')->name('change.basicinfo');
            Route::post('changeExtraInfo', 'AccountController@changeExtraInfo')->name('change.extrainfo');
            Route::post('changePassword', 'AccountController@changePassword')->name('change.password');
            Route::post('checkEmailCooldown', 'AccountController@checkEmailCooldown')->name('check.emailcooldown');
            Route::post('saveEditorWidth', 'AccountController@saveEditorWidth')->name('save.editorwidth');
            Route::post('saveEditorTheme', 'AccountController@saveEditorTheme')->name('save.editortheme');
        });

        Route::group(['prefix' => 'abuse', 'as' => 'abuse.'], function () {
            Route::post('report', 'AbuseController@report')->name('report');
        });

        Route::group(['prefix' => 'dojo', 'as' => 'dojo.'], function () {
            Route::post('dojo', 'DojoController@complete')->name('complete');
        });
    });
});

if(config("function.register")){
    Auth::routes(['verify' => true]);
} else {
    Auth::routes(['verify' => true, 'register' => false]);
}
