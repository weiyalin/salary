<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Response;

class CheckWeixinLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = session('h5_user');

        if (!$user)
        {
            $appId = get_qy_appid();
            $secret = get_qy_salary_secret();

            $auth = new \Stoneworld\Wechat\Auth($appId, $secret);

            if (!isset($_GET['auth_code']) || !isset($_GET['state'])) {
                $result = $auth->authorize();

                if (isset($result['UserId']))
                {
                    $code = $result['UserId'];
                }
                else
                {
                    return Response(view('mobile/message', [
                        'message' => '没有您的用户信息，请联系管理员同步微信通讯录'
                    ]));
                }

                $user = User::where('code', $code)->first();

                if (!$user)
                {
                    return Response(view('mobile/message', [
                        'message' => '没有您的用户信息，请联系管理员同步微信通讯录'
                    ]));
                }

                session(['h5_user' => $user]);
            }
        }

        return $next($request);
    }
}
