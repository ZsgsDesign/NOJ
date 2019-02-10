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

Route::get('/problem', 'MainController@problem')->name('problem');

Route::get('/problem/{pcode}', 'ProblemController@detail')->name('problem_detail');

Auth::routes();
