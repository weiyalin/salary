<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use App\Models\Sbuscribe;

class Submit extends Model
{
    //提交的表单
    static function submit($form_id, $content, $user_id = 0, $ip = "", $openid = ""){
        if($form_id == false){
            return responseToJson(1,'表单id为空');
        }

        $location = '';
        if($ip){
            $result = Stat::get_ip($ip);
            $location = $result["data"]["region"] . ' ' . $result["data"]["city"];
        }

        $form = DB::table('forms')->where('id',$form_id)->first();
        if($user_id && $user_id != $form->user_id){
            $count = DB::table('user_submits')->where('form_id',$form->id)->where('user_id',$user_id)->count();
            if($count){
                return responseToJson(1,'您已经提交过表单啦');
            }
        }

        $submit = $content->submit;
        $stat_list = [];
        //"name":{"value":"John McClane","type":"text","number":1,"title":"Name"},

        $user_submit = [
            'user_id'=>$user_id,
            'ip'=>$ip,
            'form_id'=>$form_id,
            'location'=>$location,
            'create_time'=>millisecond(),
            'content'=>json_encode($content),
            'openid'=>$openid
        ];

        $id = DB::table('user_submits')->insertGetId($user_submit);

        //更新统计:如果不存在则添加
        $form_stat_list = DB::table('form_stat')->where('form_id',$form_id)->get()->toArray();

        $form_stat_unique = DB::table('form_stat')->where('form_id',$form_id)->pluck("form_item_unique")->toArray();

        if(!empty($form_stat_list)){
            foreach($submit as $key => $item){
                if(!in_array($key, $form_stat_unique)){
                    if(!empty($item->enum)){
                        $json = ['type'=>$item->type,'data'=>[]];
                        if($item->type == "checkbox"){
                            $a1 = array_intersect($item->enum, $item->value);
                            $a2 = array_diff($item->enum, $item->value);
                            foreach($a1 as $a){
                                $json['data'][] = ['name'=>$a,'value'=>1];
                            }
                            foreach($a2 as $b){
                                $json['data'][] = ['name'=>$b,'value'=>0];
                            }
                        }else{
                            foreach($item->enum as $option){
                                if($item->value == $option){
                                    $json['data'][] = ['name'=>$option,'value'=>1];
                                }else{
                                    $json['data'][] = ['name'=>$option,'value'=>0];
                                }
                            }
                        }
                    }else {
                        $json = ['type'=>$item->type,'data'=>[]];
                        $json['data'][] = ['name'=>$item->value,'value'=>1];
                    }

                    $stat_list = [
                        'form_id' => $form_id,
                        'form_item_id' => $item->number,
                        'form_item_unique' => $key,
                        'form_item_name' => $item->title,
                        'form_item_json_stat' => json_encode($json),
                        'user_submit_id' => $id
                    ];

                    DB::table('form_stat')->insert($stat_list);
                }else{
                    foreach($form_stat_list as $k => $value){
                        if($value->form_item_unique == $key){
                            $stat = json_decode($value->form_item_json_stat);
                            $form_item_options = array();
                            foreach($stat->data as $i => $option){
                                array_push($form_item_options, $option->name);
                            }
                            if($item->type == "radio" || $item->type == "select"){
                                if(!in_array($item->value, $form_item_options)){
                                    $stat->data[] = ['name'=>$item->value,'value'=>1];
                                }else{
                                    foreach($stat->data as $a => $b){
                                        if($b->name == $item->value){
                                            $stat->data[$a]->value++;
                                        }
                                    }
                                }
                            }

                            if($item->type == "checkbox"){     
                                foreach($item->value as $v){
                                    if(!in_array($v, $form_item_options)){
                                        $stat->data[] = ['name'=>$v,'value'=>1];
                                    }else{
                                        foreach($stat->data as $c => $d){
                                            $d = (object)$d;
                                            if($d->name == $v){
                                                $stat->data[$c]->value++;
                                            }
                                        }
                                    }
                                }
                            }

                            DB::table('form_stat')->where('id',$value->id)->update(['form_item_json_stat'=>json_encode($stat)]);
                        }
                    }
                }
            }
        } else {
            foreach($submit as $key => $item){
                if(!empty($item->enum)){
                    $json = ['type'=>$item->type,'data'=>[]];
                    if($item->type == "checkbox"){
                        $a1 = array_intersect($item->enum, $item->value);
                        $a2 = array_diff($item->enum, $item->value);
                        foreach($a1 as $a){
                            $json['data'][] = ['name'=>$a,'value'=>1];
                        }
                        foreach($a2 as $b){
                            $json['data'][] = ['name'=>$b,'value'=>0];
                        }
                    }else{
                        foreach($item->enum as $option){
                            if($item->value == $option){
                                $json['data'][] = ['name'=>$option,'value'=>1];
                            }else{
                                $json['data'][] = ['name'=>$option,'value'=>0];
                            }
                        }
                    }
                }else {
                    $json = ['type'=>$item->type,'data'=>[]];
                    $json['data'][] = ['name'=>$item->value,'value'=>1];
                }

                $stat_list[] = [
                    'form_id' => $form_id,
                    'form_item_id' => $item->number,
                    'form_item_unique' => $key,
                    'form_item_name' => $item->title,
                    'form_item_json_stat' => json_encode($json),
                    'user_submit_id' => $id
                ];
            }

            DB::table('form_stat')->insert($stat_list);
        }

        //更新历史
        $now = millisecond();
        $year = date('Y',$now/1000);
        $month = date('m',$now/1000);
        $day = date('d',$now/1000);
        DB::table('submit_history')->insert([
            'user_id'=>$user_id,
            'form_id'=>$form_id,
            'type'=>1,
            'create_time'=>$now,
            'year'=>$year,
            'month'=>$month,
            'day'=>$day
        ]);

        //更新form统计
        DB::table('forms')->where('id',$form_id)->increment('feedback_count', 1, ["feedback_time" => millisecond()]);

        $subscribe = new Subscribe($form_id, $submit);
        $subscribe->push();

        return responseToJson(0,'提交成功');
    }

    //获取表单
    static function url_form($url){
        try {
            $form = DB::table('forms')->where('url_code',$url)->first();

            return $form;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    //判断用户是否提交
    static function submit_form($form_id, $user_id){
        try {
            $count = DB::table('user_submits')->where('form_id',$form_id)->where('user_id',$user_id)->count();

            return $count;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }

    //浏览记录
    static function view_form($form_id, $user_id = 0){
        try {
            $now = millisecond();
            $year = date('Y',$now/1000);
            $month = date('m',$now/1000);
            $day = date('d',$now/1000);
            DB::table('submit_history')->insert([
               'user_id'=>$user_id,
                'form_id'=>$form_id,
                'type'=>0,
                'create_time'=>$now,
                'year'=>$year,
                'month'=>$month,
                'day'=>$day
            ]);
            //更新view_count
            $result = DB::table('forms')->where('id',$form_id)->increment('view_count',1);   

            return $result;
        } catch (\Exception $e) {
            Log::info($e);
            return null;
        }
    }
}
