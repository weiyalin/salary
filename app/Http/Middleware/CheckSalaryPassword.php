<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Stoneworld\Wechat\AccessToken;
use Log;

class CheckSalaryPassword
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

        // 有密码存在且密码不为空
        if (isset($user->salary_password) && !empty($user->salary_password) && (!isset($user['salary_password_checked']) || !$user['salary_password_checked']))
        {
            $path = $request->path();
            if ($path != 'h5/password')
            {
                session(['redirect_path' => $path]);

                return redirect('/h5/password');
            }
        }

        return $next($request);
    }
}
