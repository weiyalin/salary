<?php

namespace App\Http\Middleware;

use \Illuminate\Http\Request;
use Closure;
use DB;
use Log;

/**
 *  微信公众号配置类
 */
class WechatConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $wxConfig = DB::table('wx_config')->first();
        if (empty($wxConfig)) {
            //TODO 未配置微信相关参数,系统不能使用
        }
        //TODO 缓存微信配置
        $wechatConfig = config('wechat');
        $wechatConfig['app_id'] = $wxConfig->appid;
        $wechatConfig['secret'] = $wxConfig->appsecret;
        $wechatConfig['token'] = $wxConfig->dev_token;
        $wechatConfig['aes_key'] = $wxConfig->dev_aeskey;
        $wechatConfig['app_name'] = $wxConfig->appname;

        //TODO 此处需要初始化微信配置,在app('wechat',$config)不起作用
        app()['config']->set('wechat', $wechatConfig);
        $request->wxConfig = $wechatConfig;
        return $next($request);
    }
}
