<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use Stoneworld\Wechat;
use DB;
use Log;

class QyWechat extends Model
{
    /**
     * @param $touser 成员ID列表（消息接收者，多个接收者用‘|’分隔，最多支持1000个）。特殊情况：指定为@all，则向该企业应用的全部成员发送
     */
    static function send_message($touser,$url,$year,$month){
        $count = count($touser);
        if($count > 1000){
            $list = [];
            for($i=0;$i<$count;$i+=1000){
                $list[] = array_slice($touser,$i,1000);
            }

        }
        else {
            $list = [$touser];
        }


        $config = DB::table('config')->first();
        $news = new Wechat\Messages\News();
        $item = new Wechat\Messages\NewsItem();
        $item->title = str_replace('M',$month,str_replace('yyyy',$year,$config->salary_notify_title));
        $item->description = str_replace('M',$month,str_replace('yyyy',$year,$config->salary_notify_caption));
        $item->pic_url = env('APP_URL').'/salary/notify_img?name='.$config->salary_notify_pic;//'http://qy.bhuitong.com/images/tongzhi.jpg';
        $item->url = $url;

        $news->item($item);

        $broadCast = new Wechat\Broadcast(get_qy_appid(),get_qy_salary_secret());
        $agent_id = get_qy_salary_agent();
        Log::info('agent:'.$agent_id);
        $broadCast->fromAgentId($agent_id);
        $broadCast->send($news);

        foreach($list as $val){
            //$str = implode("|",$val);
            Log::info($val);
            $broadCast->to($val);
        }

        return 1;
    }

    /**
     * 同步成员部门
     */
    static function sync(){
        //同步所有部门
        $group = new Wechat\Group(get_qy_appid(),get_qy_salary_secret());
        $departments = $group->lists();
        //dd($departments);
        $insert_list = [];
        foreach($departments as $item){
            $count = DB::table('org')->where('id',$item['id'])->count();
            if($count == 0){
                $insert_list[] = [
                    'id'=>$item['id'],
                    'name'=>$item['name'],
                    'parent_id'=>$item['parentid'],
                    'sort_factor'=>$item['order']
                ];
            }
            else {
                DB::table('org')->where('id',$item['id'])->update([
                    'name'=>$item['name'],
                    'parent_id'=>$item['parentid'],
                    'sort_factor'=>$item['order']
                ]);
            }
        }

        if($insert_list){
            DB::table('org')->insert($insert_list);
        }

        //更新部门path
        foreach($departments as $item){
            $parent_id = $item['parentid'];
            $path_list = [];
            while($parent_id > 0){
                $path_list[] = $parent_id;
                $parent_id = DB::table('org')->where('id',$parent_id)->value('parent_id');
            }
            $path_list[] = 0;
            sort($path_list);

            $str = "-".implode("-",$path_list)."-";

            DB::table('org')->where('id',$item['id'])->update(['path'=>$str]);
        }


        //同步成员
        $user = new Wechat\User(get_qy_appid(),get_qy_salary_secret());

        $pid = DB::table('org')->min('parent_id');
        $list = DB::table('org')->select('id','name')->where('parent_id',$pid)->get();
        //$insert_list = [];
        foreach($list as $item){
            $id = $item->id;
            $result = $user->lists($id);
            //dd($result);
            $member_list = $result['userlist'];
            //dd($member_list);
            foreach($member_list as $member){
                $m = [
                    'code'=>$member['userid'],
                    'name'=>$member['name'],
                    'email'=>array_get($member,'email',null),
                    'mobile'=>array_get($member,'mobile',null),
                    //'department'=>implode(",",$member['department']),
                    'avatar'=>array_get($member,'avatar',''),
                    'status'=>$member['status'],
                    'sync_time'=>millisecond()
                ];

                $count = DB::table('user')->where('code',$member['userid'])->count();
                if($count == 0){
                    $user_id = DB::table('user')->insertGetId($m);

                    //$insert_list[] =$m;
                }
                else {
                    DB::table('user')->where('code',$member['userid'])->update($m);
                    $user_id = DB::table('user')->where('code',$member['userid'])->value('id');
                }

                //添加部门
                $list = $member['department'];
                $depart_list = [];
                foreach($list as $val){
                    $depart_list[] = ['user_id'=>$user_id,'org_id'=>$val];
                }

                DB::table('user_org')->where('user_id',$user_id)->delete();
                DB::table('user_org')->insert($depart_list);

            }
        }



//        if($insert_list){
//            DB::table('user')->insert($insert_list);
//        }

        return responseToJson(0,'同步完成','同步完成');
    }

    static function callback(){
        $auth_code = Input::get('auth_code');
         Log::info('auth_code:'.$auth_code);
        if($auth_code){
            $auth = new Wechat\Auth(get_qy_appid(),get_qy_salary_secret());
            $auth_user = $auth->user();
             Log::info($auth_user);

            $code = $auth_user['UserId'];

            $user = DB::table('user')->where('code',$code)->first();
            $roleIds = explode(',', $user->role_id);
            if($roleIds == false){
                return response("用户未授权",401);
            }
            $permission = Role::get_role_permission($roleIds);
            session(['user' => $user, 'permission' => $permission]);


        }
        return false;
    }

}
