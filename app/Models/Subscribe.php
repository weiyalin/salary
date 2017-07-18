<?php

namespace App\Models;

use Log;

class Subscribe
{
    //
    public $form_id;
    public $submit;

    public function __construct($form_id,$submit)
    {
        //
        $this->form_id = $form_id;
        $this->submit = $submit;
    }

    /**
     * 将自身对象入队,等待队列处理发送通知
     */
    public function push()
    {
        Log::info('push');
        if ($this->form_id) {
            Log::info('dispatch:' . json_encode($this));
            $job = (new \App\Jobs\Subscribe($this->form_id,$this->submit))->onQueue('default');
            dispatch($job);

        }

    }
}
