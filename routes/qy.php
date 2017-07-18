<?php
/**
 * Created by PhpStorm.
 * User: wangzhiyuan
 * Date: 2017/6/10
 * Time: 下午6:57
 */

Route::group(['middleware' => ['web', 'checkLogin'], 'prefix' => 'salary'], function () {

    Route::get("/templates", 'Finance\TemplateController@lists');
    Route::post("/template_save", 'Finance\TemplateController@save');
    Route::post("/template_del", 'Finance\TemplateController@del');

    Route::post("/template_import", 'Finance\TemplateController@import');
    Route::get("/template_download", 'Finance\TemplateController@download');

    Route::get("/options", 'Finance\ImportController@templates');
    Route::post("/import", 'Finance\ImportController@import');
    Route::post("/import_file", 'Finance\ImportController@import_file');

    Route::get("/notify_get", 'Finance\SettingController@get');
    Route::post("/notify_save", 'Finance\SettingController@save');
    Route::post("/notify_upload", 'Finance\SettingController@upload');
    Route::get("/member_search", 'Finance\MemberController@lists');
    Route::get("/member_sync", 'Finance\MemberController@sync');
    Route::get("/member_options", 'Finance\MemberController@options');
    Route::get("/member_tree", 'Finance\MemberController@tree');
    Route::post("/member_clear", 'Finance\MemberController@clear');
    Route::get("/member_get_manage", 'Finance\MemberController@get_manage');
    Route::post("/member_save_manage", 'Finance\MemberController@save_manage');
    Route::get('/member_export', 'Finance\MemberController@export');

    Route::get("/feedback_lists", 'Finance\FeedbackController@lists');
    Route::post("/feedback_reply", 'Finance\FeedbackController@reply');

    Route::get("/notify_test", 'Finance\SettingController@notify_test');

    Route::get("/get", 'Finance\SalaryController@get_salary');
    Route::get("/list", 'Finance\SalaryController@get_list');
    Route::post("/delete", 'Finance\SalaryController@delete_salary');
    Route::post("/set_password", 'Finance\SalaryController@set_password');
    Route::post("/auth_password", 'Finance\SalaryController@auth_password');
    Route::post("/check_password", 'Finance\SalaryController@check_password');

    Route::get("/apply_search", 'Finance\ApplyController@lists');
    Route::get("/apply_options", 'Finance\ApplyController@options');
    Route::post("/apply_delete", 'Finance\ApplyController@delete');

    Route::group(['middleware' => ['admin_salary_password']], function () {
        Route::get("/detail/list", 'Finance\SalaryController@get_detail_list');
        Route::post("/detail/send", 'Finance\SalaryController@send_salary');
        Route::post("/detail/delete", 'Finance\SalaryController@delete_detail_salary');
        Route::post("/detail/reply", 'Finance\SalaryController@reply_feedback');
    });
});

Route::group(['prefix' => 'salary'], function () {
    Route::get("/notify_img", 'Finance\SettingController@download_img');
});


Route::get("/qy_url", 'User\LoginController@qy_url');
Route::get("/qy_callback", 'User\LoginController@qy_callback');
Route::get("/qy_code_callback", 'User\LoginController@qy_code_callback');
