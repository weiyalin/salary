<?php

namespace App\Http\Controllers\User;

use App\Models\QyWechat;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Response;
use DB;
use Log;
use Redirect;
use App\Models\Role;
use Stoneworld\Wechat;


class LoginController extends Controller
{
    function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $name = $request->name;//code or mobile
            $pwd = $request->pwd;
            $user = User::get_user_by_code($name);

            //TODO 目前不允许学生登录
            if ($user && $user->status != 2) {//激活状态（1：已激活，2：已禁用，3：未激活）

                //md5(md5($pwd) . $user->salt)
                if (get_md5_password($pwd, $user->salt) == $user->password) {
                    $roleIds = explode(',', $user->role_id);
                    if ($roleIds == false) {
                        return Response::json(['status' => 1, 'msg' => '当前用户未授权']);
                    }
                    $permission = Role::get_role_permission($roleIds);
                    session(['user' => $user, 'permission' => $permission]);

                    User::history([
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'create_time' => millisecond()
                    ]);
                    Log::error(['LOGIN SUCCESS' => \GuzzleHttp\json_encode($user)]);
                    return Response::json(['status' => 0, 'msg' => '登陆成功']);
                } else {
                    Log::error(['LOGIN ERROR' => \GuzzleHttp\json_encode($user)]);
                    return Response::json(['status' => 1, 'msg' => '用户名或密码错误,请重新输入']);
                }

            } else {
                Log::error(['LOGIN ERROR' => \GuzzleHttp\json_encode($user)]);
                return Response::json(['status' => 1, 'msg' => '用户名或密码错误,请重新输入']);
            }

        } else {
            if(env('QY')){
                //企业微信
                $wx_login_url = 'https://open.work.weixin.qq.com/wwopen/sso/qrConnect?appid=' .get_qy_appid().'&agentid='. get_qy_salary_agent() . '&redirect_uri=' . urlencode(env('APP_URL') . '/qy_callback');
            }
            else {
                //企业号
                $wx_login_url = 'https://qy.weixin.qq.com/cgi-bin/loginpage?corp_id=' . env('CorpID') . '&redirect_uri=' . urlencode(env('APP_URL') . '/qy_code_callback');

            }
            return view('login')->with('wx_login_url', $wx_login_url);
        }
    }

    function logout(Request $request)
    {
        //session(['user' => null, 'permission' => null]);
        $request->session()->flush();
        return Response::json(['status' => 0, 'msg' => '登出成功']);
    }

    function qy()
    {
        return view('qy');
    }

    function qy_url()
    {
        $config = [
            'appid' => get_qy_appid(),
            'agentid' => get_qy_salary_agent(),
            'redirect_uri' => urlencode(env('APP_URL') . '/qy_callback'),
            'state' => 'gamma'
        ];
        return responseToJson(0, 'success', $config);
    }


    function qy_code_callback()
    {
        Log::info('qy_code_callback');
        $authorize = new Wechat\Authorize(get_qy_appid(), get_qy_salary_secret());
        $auth_user = $authorize->user();
        Log::info($auth_user);

        if ($auth_user['usertype'] == 1) {
            //超级管理员
            $email = $auth_user['user_info']['email'];
            $user = DB::table('user')->where('email', $email)->first();
        } else {
            //分级管理员
            $code = $auth_user['user_info']['userid'];
            $user = DB::table('user')->where('code', $code)->first();
        }


        if ($user == false) {
            return response('用户不存在', 401);
        }

        $roleIds = explode(',', $user->role_id);
        if ($user->is_super_manager == false && $user->is_grade_manager == false) {
            return redirect('/errors/401');
        }
        $permission = Role::get_role_permission($roleIds);
        session(['user' => $user, 'permission' => $permission]);

        return redirect("/");

    }


    function qy_callback()
    {
        $auth_code = Input::get('auth_code');
        Log::info('auth_code:' . $auth_code);

        $auth = new Wechat\Auth(get_qy_appid(), get_qy_salary_secret());
        $auth_user = $auth->user();
        Log::info($auth_user);

        $code = $auth_user['UserId'];

        $user = DB::table('user')->where('code', $code)->first();

        if ($user == false) {
            return response('用户不存在', 401);
        }

        $roleIds = explode(',', $user->role_id);
        if ($user->is_super_manager == false && $user->is_grade_manager == false) {
            return redirect('/errors/401');
        }
        $permission = Role::get_role_permission($roleIds);
        session(['user' => $user, 'permission' => $permission]);

        return redirect("/");
    }


    function auth_callback()
    {
        echo '非法访问';
        exit;
        //$user = Input::get('user');
        //dd(json_decode($user));
//        $openid = Input::get('openid');
//        $curl = new \anlutro\cURL\cURL();
//        $url = env('NOTIFY_URL') . '/api/auth_user';
//        Log::info('url:' . $url);
//        $real_openid = RsaCrypt::private_decode(utf8_decode($openid));
//        $rsa_openid = RsaCrypt::encode($real_openid);
//        $response = $curl->post($url, ['openid' => $rsa_openid, 'code' => env('AUTH_CODE')]);
//        if ($response && $response->statusCode == 200) {
//            $result = json_decode($response->body);
//            if ($result->code == 0) {
//                $user = $result->result;
//                $user->role_id = 1;
//                $permission = Role::get_role_permission(1);//todo: 管理员登录,默认为超级管理员
//                session(['user' => $user, 'permission' => $permission]);
//
//                User::history([
//                    'user_id' => $user->id,
//                    'user_name' => $user->real_name,
//                    'login_type' => 2,
//                    'unique_id' => $real_openid,
//                    'create_time' => millisecond()
//                ]);
//
//                return Response::json(['status' => 0, 'msg' => '登陆成功']);
//            } else {
//                return Response::json(['status' => 1, 'msg' => $result->msg]);
//            }
//
//        } else {
//            Log::error($response);
//            return Response::json(['status' => 1, 'msg' => '网络错误,请稍候尝试']);
//        }
    }
}