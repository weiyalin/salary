<?php

namespace App\Console\Commands;

use App\Models\RsaCrypt;
use Illuminate\Console\Command;
//use CUrl;
use Log;

class GammaGo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gamma:notify {host}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'general licence in this site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $public_path = RsaCrypt::public_key_path();
        $private_path = RsaCrypt::private_key_path();

        //获取域名
        $host = $this->argument('host');
//        if($host == false){
//            $this->error('域名不能为空');
//            return;
//        }

        //生成秘钥
//        $privkey='';
//        $res = openssl_pkey_new();
//        openssl_pkey_export($res, $privkey);
//        //$this->info($privkey);
//        $pubkey=openssl_pkey_get_details($res);
//        $pubkey=$pubkey["key"];
//        //$this->info($pubkey);
//
//        //保存秘钥
//        if(file_exists($public_path)){
//            unlink($public_path);
//        }
//        $myfile = fopen($public_path, "w") or die("Unable to open file!");
//        fwrite($myfile,$pubkey);
//        fclose($myfile);
//
//        if(file_exists($private_path)){
//            unlink($private_path);
//        }
//        $myfile = fopen($private_path, "w") or die("Unable to open file!");
//        fwrite($myfile,$privkey);
//        fclose($myfile);
//
//        //获取公钥
//        extension_loaded('openssl') or die('php需要openssl扩展支持');
//        (file_exists($public_path)) or die('公钥的文件路径不正确');
//        $content = file_get_contents($public_path);
//        $publicKey = openssl_pkey_get_public($content);
//        ($publicKey) or die('公钥不可用');
        Log::debug('auth web:'.$host);
        //请求服务器存储公钥
        $curl = new \anlutro\cURL\cURL();

        $response = $curl->post(env('NOTIFY_URL').'/api/auth_tracker',['host'=>$host]);
        if($response && $response->statusCode == 200){
            //存储codes
            $result = json_decode($response->body);
            $private_key = $result->private_key;

            //保存秘钥
            if(file_exists($private_path)){
                unlink($private_path);
            }
            $myfile = fopen($private_path, "w") or die("Unable to open file!");
            fwrite($myfile,$private_key);
            fclose($myfile);

        }
        else {
            $this->error($response);
        }

        //完成
        $this->info('秘钥配置完毕.');
    }
}
