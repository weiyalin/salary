<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use DB;

class FeedbackController extends Controller
{
    //
    function lists(Request $request){
        $pageSize = $request->page_size;
        $name = $request->name;
        $content = Input::get('content');

        $query = DB::table('feedback');
        if($name){
            $query->where('user_name','like',$name.'%');
        }
        if($content){
            $query->where('content','like',$content.'%');
        }

        $result = $query->orderBy('updated_at','desc')->paginate($pageSize);
        return responseToJson(0,'success',$result);
    }

    function reply(){
        $id = Input::get('id');
        $content = Input::get('content');

        DB::table('feedback')->where('id',$id)->update([
            'reply_content'=>$content,
            'reply_time'=>date('Y-m-d H:i:s',time()),
            'reply_user_id'=>get_user_id(),
            'reply_user_name'=>get_user_info()->name
        ]);

        return responseToJson(0,'保存成功','保存成功');
    }
}
