<?php

namespace App\Http\Controllers\Form;

use App\Models\FormUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Models\Submit;

class SubmitController extends Controller
{
    //处理表单提交数据
    function submit(){
        $form_schema = \GuzzleHttp\json_decode(Input::get("form_schema"));
        $form_id = Input::get("form_id");
        $ip = $_SERVER["REMOTE_ADDR"];

        $user_id = get_user_id();
        $openid = get_user_openid();

        $result = Submit::submit($form_id, $form_schema, $user_id, $ip, $openid);

        $form = \GuzzleHttp\json_decode($result->content());

        if ($form->code == 1) {
            return responseToJson(1, "fail", $form->msg);
        } else {
            return responseToJson(0, "success", $form->msg);
        }
    }

    /**
     * 访问表单
     */
    function url($code){
        $user_id = get_user_id();
        $ip = $_SERVER["REMOTE_ADDR"];
        $preview = Input::get("from");

        $form = Submit::url_form($code);

        if($form == false){
            return view('warn')
            ->with('code',1)
            ->with('msg','表单不存在');
        }

        if($form->status != 1){
            if($form->user_id != $user_id){
                return view('warn')
                ->with('code',1)
                ->with('msg','表单不存在');
            }
        }

/*        if($form->is_login == 1){
            $formUser = FormUser::factory();
            if($formUser->is_login() == false){
                return view('warn')
                ->with('code',2)
                ->with('msg','用户未登录');
            }

            if($form->is_only == 1){
                $count = Submit::submit_form($form->id, $user_id);
                if($count > 0){
                    return view('warn')
                    ->with('code',1)
                    ->with('msg','用户已提交');
                }
            }
        }*/

        
        if($preview && $preview == "preview"){
            $button = 0;
        }else{
            $button = 1;
            Submit::view_form($form->id, $user_id);
        }


        $app = app('wechat');
        $js = $app->js;
        $rlt = $js->config(array('onMenuShareAppMessage', 'onMenuShareTimeline'), true);
        $arr = json_decode($rlt, true);

        return view('schema')
            ->with('form_title',$form->form_name)
            ->with('id',$form->id)
            ->with('button',$button)
            ->with('title',$form->share_title)
            ->with('desc',$form->share_desc)
            ->with('pic','http://' . $_SERVER['SERVER_NAME'] . '/img/get/'  . $form->share_pic)
            ->with('js',$arr)
            ->with('schema',$form->form_schema);
        }
}
