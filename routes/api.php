<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'Api'], function () {
    Route::group(['prefix' => 'account','as' => 'account.'], function () {
        Route::post('/login', 'AccountController@login')->name("login");
    });

    Route::group(['prefix' => 'system','as' => 'system.'], function () {
        Route::post('/info', 'SystemController@info')->name("info");
    });

    Route::group(['prefix' => 'contest','as' => 'contest.','middleware' => ['auth:api']], function () {
        Route::post('/info', 'ContestController@info')->middleware(['api.contest.clearance:public_visible'])->name("info");
        Route::post('/problems', 'ContestController@problems')->middleware(['api.contest.clearance:visible'])->name("problems");
        Route::post('/status', 'ContestController@status')->middleware(['api.contest.clearance:visible'])->name("status");
        Route::post('/scoreboard', 'ContestController@scoreboard')->middleware(['api.contest.clearance:visible'])->name("scoreboard");
        Route::post('/submitSolution', 'ContestController@submitSolution')->middleware(['api.contest.clearance:participated', 'api.contest.hasProblem', 'api.contest.hasCompiler'])->name("submitSolution");
        Route::post('/clarification', 'ContestController@clarification')->middleware(['api.contest.clearance:visible'])->name("clarification");
        Route::post('/requestClarification', 'ContestController@requestClarification')->middleware(['api.contest.clearance:participated'])->name("requestClarification");
        Route::post('/fetchAnnouncement', 'ContestController@fetchAnnouncement')->middleware(['api.contest.clearance:visible'])->name("fetchAnnouncement");
    });

    Route::group(['prefix' => 'problem','as' => 'problem.','middleware' => ['auth:api']], function () {
        Route::post('/fetchVerdict', 'ProblemController@fetchVerdict')->middleware(['api.submission.exist'])->name("fetchVerdict");
    });

    Route::group(['prefix' => 'submission','as' => 'submission.','middleware' => ['auth:api']], function () {
        Route::post('/info', 'SubmissionController@info')->middleware(['api.submission.exist'])->name("info");
    });
});
