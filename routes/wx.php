<?php
Route::group(['middleware' => ['web','checkLogin']], function () {

    Route::post("/weixin/config/set", 'Wechat\WeixinController@set_config');
    Route::post("/weixin/config/get", 'Wechat\WeixinController@get_config');

    Route::post("/weixin/template/set", 'Wechat\WeixinController@set_wx_template');
    Route::post("/weixin/template/get", 'Wechat\WeixinController@get_wx_template');

    Route::post("/weixin/reply/get", 'Wechat\WeixinController@get_reply');
    Route::post("/weixin/reply/set", 'Wechat\WeixinController@set_reply');
    Route::post("/weixin/follow/set", 'Wechat\WeixinController@set_follow');
    Route::post("/weixin/news/set", 'Wechat\WeixinController@set_news');

    Route::post("/weixin/reply/create", 'Wechat\WeixinController@create_reply');
    Route::post("/weixin/reply/delete/{id}", 'Wechat\WeixinController@delete_reply');
});

Route::group(['middleware' => ['wechat.config','checkLogin']], function () {
    Route::post("/weixin/industry", 'Wechat\WeixinController@set_industry');
    Route::post("/weixin/update", 'Wechat\WeixinController@add_template');
    Route::post("/weixin/auto_update", 'Wechat\WeixinController@get_private_templates');
    Route::post("/weixin/menu/get", 'Wechat\WeixinController@get_menu');
    Route::post("/weixin/menu/set", 'Wechat\WeixinController@set_menu');
});

Route::group(['middleware' => ['wechat.config']], function () {
    Route::get('/weixin/group/get', 'Wechat\WeixinController@get_wx_group');
    Route::get('/weixin/group/set', 'Wechat\WeixinController@set_wx_group');
    Route::get('/weixin/group/move', 'Wechat\WeixinController@move_wx_group');
    Route::get('/weixin/group/del', 'Wechat\WeixinController@del_wx_group');
    Route::get('/weixin/group/menu/a/set', 'Wechat\WeixinController@set_wx_group_menu_a');
    Route::get('/weixin/group/menu/b/set', 'Wechat\WeixinController@set_wx_group_menu_b');
    Route::get('/weixin/menu/get', 'Wechat\WeixinController@get_wx_menu');
    Route::get('/weixin/user/menu/get', 'Wechat\WeixinController@get_wx_user_mennu');
});

Route::group(['prefix' => 'h5', 'middleware' => ['checkWeixinLogin', 'checkSalaryPassword']], function () {
    Route::get('', 'Mobile\SalaryController@index');
    Route::get('logout', 'Mobile\SalaryController@logout');
    Route::get('help/{id?}', 'Mobile\SalaryController@help');
    Route::get('profile', 'Mobile\SalaryController@profile');
    Route::get('bill/{year?}', 'Mobile\SalaryController@bill');
    Route::get('bill/detail/{id}', 'Mobile\SalaryController@bill_detail');
    Route::get('bill/destroy/{id}', 'Mobile\SalaryController@bill_destroy');
    Route::get('feedback/list', 'Mobile\SalaryController@feedback_list');
    Route::match(['get', 'post'], 'reset', 'Mobile\SalaryController@reset_password');
    Route::match(['get', 'post'], 'feedback', 'Mobile\SalaryController@feedback');
    Route::match(['get', 'post'], 'bill/reply/{id}', 'Mobile\SalaryController@bill_reply');
    Route::match(['get', 'post'], 'password', 'Mobile\SalaryController@password');
});
