<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Event;
use Illuminate\Support\Facades\Input;

/**
 * Class WechatAuthenticate
 */
class WechatAuthenticate
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
        //TODO 指定用户测试
        $dev_user_id = env('DEV_USER_ID', 0);
        if ($dev_user_id) {
            $oauthUser = session("wechat.oauth_user");
            if (empty($oauthUser) || !isset($oauthUser['user_id']) || intval($oauthUser['user_id']) != $dev_user_id) {
                $user = DB::table('user')->where('id', $dev_user_id)->first();
                if (!empty($user)) {
                    session(['wechat.oauth_user' => ['openid' => $user->openid], 'user_id' => $user->id]);
                }
            }
        }

        $wechat = app('wechat');//
        if (empty(session('wechat.oauth_user'))) {
            if (!empty(env("AUTH_SERVER"))) {
                if ($request->has('gm_oauth_user')) {
                    $user = \GuzzleHttp\json_decode(urldecode(Input::get("gm_oauth_user")), true);
                    if (count($user) > 0) {
                        session(['wechat.oauth_user' => ['openid' => $user["openid"]]]);
                        $this->toDo($user);
                    } else {
                        Log::info("没有拿到用户信息");
                        exit();
                    }
                } else {
                    return redirect()->to(env("AUTH_SERVER") . "?gm_from=" . $request->fullUrl());
                }
            } else {
                if ($request->has('state') && $request->has('code')) {
                    $user = $wechat->oauth->user();

                    $openid = $user->getId();
                    session(['wechat.oauth_user' => ['openid' => $openid]]);
                    //获取用户信息
                    $userService = $wechat->user;
                    $userInfo = $userService->get($openid);

                    $this->toDo($userInfo);

                    return redirect()->to($this->getTargetUrl($request));
                }

                $scopes = $request->wxConfig['oauth']['scopes'];
                if (is_string($scopes)) {
                    $scopes = array_map('trim', explode(',', $scopes));
                }
                return $wechat->oauth->scopes($scopes)->redirect($request->fullUrl());
            }
        }

        return $next($request);
    }

    /**
     * Build the target business url.
     *
     * @param Request $request
     *
     * @return string
     */
    public function getTargetUrl($request)
    {
        $queries = array_except($request->query(), ['code', 'state']);
        return $request->url() . (empty($queries) ? '' : '?' . http_build_query($queries));
    }

    private function toDo($userInfo)
    {
        $query = DB::table('wx_user')
            ->where('openid', $userInfo["openid"])
            ->first();
        $data = ['nickname' => isset($userInfo['nickname']) ? wx_nickname_filter($userInfo["nickname"]) : '',
            'avatar' => isset($userInfo["headimgurl"]) ? $userInfo["headimgurl"] : '',
            'sex' => isset($userInfo['sex']) ? intval($userInfo["sex"]) : 0,
            'province' => isset($userInfo["province"]) ? $userInfo["province"] : '',
            'city' => isset($userInfo["city"]) ? $userInfo["city"] : '',
            'country' => isset($userInfo["country"]) ? $userInfo["country"] : '',
            'is_subscribe' => isset($userInfo["subscribe"]) ? intval($userInfo["subscribe"]) : 0,
            'subscribe_time' => isset($userInfo["subscribe_time"]) ? strval(intval($userInfo["subscribe_time"]) * 1000) : 0,
            'unionid' => isset($userInfo["unionid"]) ? $userInfo["unionid"] : ''];
        if ($query) {
            DB::table('wx_user')->where('openid', $userInfo["openid"])->update($data);
        } else {
            $data['openid'] = $userInfo["openid"];
            $data['create_time'] = millisecond();
            DB::table('wx_user')->insert($data);
        }
    }
}
