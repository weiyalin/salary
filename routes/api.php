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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group([], function () {
    Route::post('login', 'Api\TerminalController@login');
    Route::get('hit', 'Api\TerminalController@hit');
    Route::get('paper', 'Api\TerminalController@exam_paper');
    Route::post('submit', 'Api\TerminalController@exam_submit');
    Route::post('image', 'Api\TerminalController@image');

});
