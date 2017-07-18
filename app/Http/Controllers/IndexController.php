<?php

namespace App\Http\Controllers;

use App\Models\QyWechat;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Log;
use Stoneworld\Wechat;


class IndexController extends Controller
{
    //
    public function index(Request $request)
    {
        //$result = QyWechat::callback();
        $auth_code = Input::get('auth_code');
       // Log::info('auth_code:'.$auth_code);
        if($auth_code){
            $auth = new Wechat\Auth(get_qy_appid(),get_qy_salary_secret());
            $auth_user = $auth->user();
            Log::info($auth_user);

            $code = $auth_user['UserId'];

            $user = DB::table('user')->where('code',$code)->first();
            $roleIds = explode(',', $user->role_id);
            if($user->is_super_manager == false && $user->is_grade_manager == false){
                return redirect('/errors/401');
            }
            $permission = Role::get_role_permission($roleIds);
            session(['user' => $user, 'permission' => $permission]);

        }

        $userInfo = get_user_info();
        if (empty($userInfo)) {
            return redirect('/login');
        }
        $profile = new \stdClass();
        $profile->id = intval($userInfo->id);
        $profile->name = $userInfo->name;
        $profile->code = $userInfo->code;

        return view('index')->with('profile', $profile);
    }

    public function error_401(Request $request)
    {
        return view('errors/401');
    }
}
