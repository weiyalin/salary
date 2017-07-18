<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use Log;

class Subscribe implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $form_id;
    protected $submit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($form_id,$submit)
    {
        //
        $this->form_id = $form_id;
        $this->submit = $submit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('subscribe process');
        try{
            $list = DB::table('subscribe')->where('form_id',$this->form_id)->get();

            foreach($list as $subscribe_user){
                $curl = new \anlutro\cURL\cURL();
                $url = $subscribe_user->subject_url;
                $response = $curl->post($url,['submit'=>$this->submit]);
                if($response && $response->statusCode == 200){
                    Log::info('subscribe success');
                } else {
                    Log::info('subscribe fail');
                }

                DB::table('subscribe')->where('id',$subscribe_user->id)->increment('count',1); 

            }

            return ;
        }
        catch(Exception $e){
            Log::error($e->getMessage());
        }
    }
}
