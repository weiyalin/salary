<?php

namespace App\Jobs;

use App\Models\Message;
use EasyWeChat\Core\Exception;
use EasyWeChat\Foundation\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use DB;

class SendNotify implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        //
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('notify..test');
        try{

            $user_list = $this->general($this->message);
            Log::info('type:'.$this->message->type);


            if(intval($this->message->slice) == 0){
                //完整的通知列表,计算发送人数
                $r = DB::table('vender_notify')->where('id',$this->message->id)->update(['send_count'=>count($user_list)]);
            }


            //分隔大数据用户
            $step = 300;
            $count = count($user_list);
            if($count > $step){
                for($i=0;$i<$count;$i+=$step){
                    $slice_list = array_slice($user_list,$i,$step);
                    //构造接收人列表
                    $receiver_list = [];
                    foreach($slice_list as $receiver){
                        $receiver_list[] = ['type'=>'user','id'=>$receiver,'name'=>''];
                    }

                    $receivers = \GuzzleHttp\json_encode($receiver_list);

                    $message = new Message($this->message->id,$this->message->title,$receivers,1);
                    $message->push();
                }
            }
            else {

                $id = $this->message->id;
                $title = $this->message->title;
                $r = $this->send_vender_message($id,$user_list);
                return ;
            }

        }
        catch(Exception $e){
            Log::error($e->getMessage());
        }
    }

    private function general($message){
        //通知接收者json串，含组织机构、单独的教师、学生[{type:user,id:user_id,openid:openid,name:‘’}]
        $receiver_list = \GuzzleHttp\json_decode($message->receiver);
        if($receiver_list == false){
            return [];
        }
        //接收人列表
        $user_list = [];
        //解析接收者
        foreach($receiver_list as $key=>$receiver){
            switch ($receiver->type){
                case 'user':
                    $user_list[] = $receiver->openid;//['user_id'=>$receiver->id,'user_name'=>'','type'=>0];
                    break;
                default:

                    break;
            }
        }

        //去重
        $user_list = array_flip(array_flip($user_list));
        //$user_list = Message::unique($user_list);

        //TODO 权限校验,【发送者是否有给接收者发送通知的权限】
        //以可管理的组织机构为准,现存组中的成员有可能已从自己管理的组织机构中移除
        //【暂时不处理,如果要处理需要考虑界面、分组、组织机构】

        return $user_list;

    }


    public function send_vender_message($id,$user_list){
        $vender_notify = DB::table('vender_notify')->where('id',$id)->first();
        Log::info('notify_id:'.$id);
        if ($vender_notify == false || $user_list == false) {
            Log::error('vender_notify 通知不存在');
            return false;
        }

        Log::info('send_vender_message beginning.');

        //获取config
        $wxConfig = DB::table('wx_config')->first();
        $options = [
            'debug' => env('APP_DEBUG', false),
            'app_id' => $wxConfig->appid,
            'secret' => $wxConfig->appsecret,
            'token' => $wxConfig->dev_token,
            'log' => [
                'level' => env('WECHAT_LOG_LEVEL', 'debug'),
                'file' => env('WECHAT_LOG_FILE', storage_path('logs/wechat.log')),
            ],
        ];

        $template = DB::table('wx_template')->where('id',$vender_notify->template_id)->first();

        //Log::info('template_id:'.json_encode($template));

        $app = new Application($options);
        $notice = $app->notice;

        $detail_list = DB::table('vender_notify_detail')
            ->where('notify_id',$vender_notify->id)
            ->whereIn('to_user_id',$user_list)
            ->leftJoin('user','vender_notify_detail.to_user_id','=','user.id')
            ->select('vender_notify_detail.*','user.openid')
            ->get();

        Log::info('detail_list count:'.count($detail_list));

        $success_count = 0;
        foreach ($detail_list as $item) {

            try {
                //构造data
                $rules = json_decode($item->template_rule);
                $data = [];
                if ($rules) {
                    foreach ($rules as $rule) {
                        $data[$rule->wx_key] = (array)$rule->value;//['value'=>$rule->value,'color'=>$rule->color];
                    }
                }


                $result = $notice->send([
                    'touser' => $item->openid,
                    'template_id' => $template->template_id,
                    'url' => $item->url,
                    'data' => $data,
                ]);
                //Log::error($result);
            } catch (\Exception $e) {
                $result = new \stdClass();
                $result->errcode = 1;
                $result->errmsg = $e->getMessage();
            }

            $condition = [];

            if ($result->errcode == 0) {
                //发送成功
                $condition = [
                    'is_success' => 1,
                    'wx_msg_id' => $result->msgid,
                    'failed_reason' => '',
                    'wx_err_msg' => ''
                ];

                $success_count++;
            }
//            else if ($result->errcode == 45011) {
//                //API调用太频繁，请稍候再试
//                Log::error('API调用太频繁,入队重试:' . \GuzzleHttp\json_encode($item));
//                $receiver_list = ['type' => 'user', 'id' => $item->to_user_id, 'name' => ''];
//
//                $receivers = \GuzzleHttp\json_encode($receiver_list);
//
//                $message = new Message($vender_notify->id, $vender_notify->title, $receivers,0,1);
//                $message->push();
//
//                continue;
//
//            }
            else {
                //发送失败
                $condition = [
                    'is_success' => 0,
                    'wx_err_msg' => $result->errmsg,
                    'failed_reason' => '未知错误',
                    'wx_msg_id' => '',
                ];
            }

            //保存
            $r = DB::table('vender_notify_detail')->where('id', $item->id)->update($condition);
        }

        $r = DB::table('vender_notify')->where('id', $vender_notify->id)->increment('send_success_count', $success_count);


        return true;

    }



}
