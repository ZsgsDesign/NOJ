<?php

/*
|--------------------------------------------------------------------------
| SPA Routes
|--------------------------------------------------------------------------
|
| Here is where you can register SPA routes for your frontend. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "spa" middleware group.
|
*/

Route::get('{any}', SpaController::class)->where('any','.*');

// Route::get('{any}', SpaController::class)->where('any','(.*)');
