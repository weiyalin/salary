<?php

Route::group(['middleware'=>'checkLogin'],function(){
    Route::group(['prefix' => 'teacher'], function () {
        Route::get('/list', 'User\TeacherController@lists');
        Route::get('/get', 'User\TeacherController@get');
        Route::post('/edit', 'User\TeacherController@edit');
        Route::post('/delete', 'User\TeacherController@delete');
        Route::post('/reset', 'User\TeacherController@reset');
        Route::post('/import', 'User\TeacherController@import');
        Route::get('/template', 'User\TeacherController@template');

    });
    Route::group(['prefix' => 'student'], function () {
        Route::get('/list', 'User\StudentController@lists');
        Route::get('/get', 'User\StudentController@get');
        Route::post('/edit', 'User\StudentController@edit');
        Route::post('/delete', 'User\StudentController@delete');
        Route::post('/reset', 'User\StudentController@reset');
        Route::post('/import', 'User\StudentController@import');
        Route::get('/template', 'User\StudentController@template');

    });
    Route::group(['prefix' => 'analysis'], function () {
        Route::get('/list', 'Analysis\ExamController@lists');
        Route::get('/stat', 'Analysis\ExamController@stat');

    });
});

