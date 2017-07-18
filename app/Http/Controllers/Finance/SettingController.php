<?php

namespace App\Http\Controllers\Finance;

use App\Models\QyWechat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;

class SettingController extends Controller
{
    //
    function get(){
        $config = DB::table('config')->first();
        return responseToJson(0,'success',$config);
    }


    function save(){
        $title = Input::get('title');
        $caption = Input::get('caption');
        $pic = Input::get('pic');
        $content = Input::get('content');
        $is_feedback = intval(Input::get('is_feedback'));
        $is_destroy = intval(Input::get('is_destroy'));

        $data = [
            'salary_notify_title'=>$title,
            'salary_notify_caption'=>$caption,
            'salary_notify_pic'=>$pic,
            'salary_care_content'=>$content,
            'is_enable_feedback'=>$is_feedback,
            'is_enable_destroy'=>$is_destroy,
            'update_user_id'=>get_user_id(),
            'update_time'=>millisecond()
        ];

        $config = DB::table('config')->first();
        if($config){
            DB::table("config")->where('id',$config->id)->update($data);
        }
        else {
            DB::table('config')->insert($data);
        }

        return responseToJson(0,'保存成功','保存成功');

    }


    function notify_test(){
        QyWechat::send_message(['qy01a763117528b7009b62d32f82','qy01d763a575b0b7079b61aeca2b','QiYiChao'],"http://www.baidu.com",2017,6);
        //QyWechat::sync();
        echo 'hello';
        return 1;
    }


    function download_img(){
        $name = Input::get('name');
        $path = storage_path().'/app/notify/'.$name;
        return response()->download($path);
    }

    function upload(Request $request){
        //判断请求中是否包含name=file的上传文件
        if (!$request->hasFile('files')) {
            return responseToJson(1, '上传文件为空！');
        }
        $file = $request->file('files');
        //判断文件上传过程中是否出错
        if (!$file->isValid()) {
            return responseToJson(2, '文件上传出错！');
        }
        $destPath = realpath(storage_path('app/notify'));
        if (!file_exists($destPath))
            mkdir($destPath, 0777, true);
        //$filename = $file->getClientOriginalName();
        $filename = get_user_id() . 'and' . millisecond().'.' . $file->getClientOriginalExtension();
        if ($file->move($destPath, $filename) == false) {
            return responseToJson(3, '保存文件失败！');
        }


        //excel导入
        //$filePath = 'storage/app/tmp/' . $filename;

        return responseToJson(0,'success',$filename);

    }

}
