<?php

Route::get('/warning/{id}', function ($id) {
    if($id == 0){
        return view('warn')
            ->with('code',1)
            ->with('msg',"提交失败，请返回页面重新提交！");
    }elseif($id == 1){
        return view('warn')
            ->with('code',0)
            ->with('msg',"提交成功，感谢您的参与！");
    }elseif($id == 2){
		return view('warn')
			->with('code',2)
			->with('msg','用户未登录');
    }elseif($id == 3){
        return view('warn')
            ->with('code',1)
            ->with('msg','用户已提交');
    }
});
