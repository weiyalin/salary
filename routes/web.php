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

Route::get('/', 'IndexController@index');

Route::get('/ip', function () {
    $address = \App\Models\Stat::get_ip($_GET["ip"]);
    return responseToJson(0, 'success', $address);
});

Route::post('/feedback', 'Feedback\FeedbackController@report');
Route::post('/feedback/save', 'Feedback\FeedbackController@save');
Route::get('/feedback/get_feedback_list', 'Feedback\FeedbackController@get_feedback_list');
Route::get('/feedback/detail', 'Feedback\FeedbackController@detail');
Route::get('/feedback/download', 'Feedback\FeedbackController@download');

Route::post('/auth_callback', 'User\LoginController@auth_callback');

Route::group(['prefix' => 'form', 'middleware' => 'checkLogin'], function () {
    Route::get('', 'Form\FormController@get_list');
    Route::get('detail', 'Form\FormController@detail');
    Route::post('create', 'Form\FormController@create');
    Route::post('save', 'Form\FormController@save');
    Route::post('edit', 'Form\FormController@edit');
    Route::post('upload', 'Form\FormController@upload');
    Route::get('components', 'Form\FormController@components');
    Route::get('download', 'Form\FormController@download');
    Route::post('template/create', 'Form\FormController@create_template');
    Route::get('group', 'Form\FormController@get_group_list');
    Route::post('group/edit', 'Form\FormController@form_group_edit');
    Route::post('group/create', 'Form\FormController@group_create');
    Route::post('group/delete', 'Form\FormController@group_delete');
});

Route::get('/form/selector', 'Form\PublishController@get_data');
Route::get('/form/selector/search', 'Form\PublishController@get_search');
Route::post('/form/update', 'Form\PublishController@update_wxshare');
Route::post('/form/data/get', 'Form\PublishController@get_wxshare');
Route::get("/img/get/{img_name}", 'Form\PublishController@get_img');
Route::post('/upload', 'Form\PublishController@upload_image');
Route::post('/form/send', 'Form\PublishController@send');
Route::post('/form/release/set', 'Form\PublishController@set_form');
Route::post('/form/release/get', 'Form\PublishController@get_form');
Route::get('/form/export', 'Form\PublishController@export_form');

Route::post('/submit/upload', 'Form\PublishController@upload_submit');
Route::post('/form/login/set', 'Form\PublishController@set_form_login');

include('c.php');
include('wx.php');
include('warn.php');
include('user.php');
include('qy.php');


Route::group(['middleware' => ['wechat.config']], function () {
    Route::post('/forms/submit', 'Form\SubmitController@submit');
    //Route::get('/submit/{code}', 'Form\SubmitController@url');
});

Route::group(['middleware' => ['wechat.config', 'checkForm']], function () {
    Route::get('/submit/{code}', 'Form\SubmitController@url');
});

Route::get('errors/401', 'IndexController@error_401');