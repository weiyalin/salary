<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use App\Models\FormUser;
use Event;
use Illuminate\Support\Facades\Input;

/**
 * Class CheckForm
 */
class CheckForm
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
    	$code = $request->route('code');
    	$form = $this->getForm($code);

        if($form->is_login == 1){
            $formUser = FormUser::factory();
            if($formUser->is_login() == false){
            	return redirect('/warning/2');
            }
    	}

        if($form->is_wx == 1){
            $wechat = app('wechat');
            if (empty(session('wechat.oauth_user'))) {
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

        if($form->is_only == 1){
            if(get_user_id() != 0){
            	$count = $this->userSubmits($form->id, get_user_id());
            }elseif(get_user_openid() != ""){
            	$count = $this->wxSubmits($form->id, get_user_openid());
            }else{
            	$count = $this->ipSubmits($form->id, $_SERVER["REMOTE_ADDR"]);
            }

            if($count > 0){
            	return redirect('/warning/3');
            }
    	}

        return $next($request);
    }

    public function getForm($code)
    {
        $form = DB::table('forms')->where('url_code',$code)->first();
        return $form;
    }

    public function userSubmits($form_id, $user_id)
    {
        $count = DB::table('user_submits')->where('form_id',$form_id)->where('user_id',$user_id)->count();
        return $count;
    }

    public function wxSubmits($form_id, $openid)
    {
        $count = DB::table('user_submits')->where('form_id',$form_id)->where('user_id',$user_id)->count();
        return $count;
    }

    public function ipSubmits($form_id, $if)
    {
        $count = DB::table('user_submits')->where('form_id',$form_id)->where('user_id',$user_id)->count();
        return $count;
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
