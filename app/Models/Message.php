<?php

namespace App\Models;

use App\Jobs\SendNotify;
use Log;

class Message
{
    //
    public $id;
    public $title;
    public $receiver;
    public $slice = 0;

    /**
     * Message constructor.
     * @param $id 通知id
     * @param $title 通知标题
     * @param $receiver 接收人列表,json格式字符串(目前只支持type=user)[{type:user,id:user_id,openid:openid,name:‘’}]
     * @param $slice 是否是分隔片段
     */
    public function __construct($id, $title, $receiver, $slice = 0)
    {
        //
        $this->id = $id;
        $this->title = $title;
        $this->receiver = $receiver;
        $this->slice = $slice;
    }

    /**
     * 将自身对象入队,等待队列处理发送通知
     */
    public function push()
    {
        Log::info('push');
        if ($this->id) {
            Log::info('dispatch:' . json_encode($this));
            $job = (new SendNotify($this))->onQueue('default');
            dispatch($job);
        }
    }



}
