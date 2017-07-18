<?php

namespace App\Http\Middleware;

use App\Models\Salary;
use App\Models\User;
use Closure;
use Stoneworld\Wechat\AccessToken;
use Log;

class CheckAdminSalaryPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $pass = true;
        $salary = Salary::where('id', $request->salary_id)->first();
        if ($salary && $salary->password) {//需要验证密码
            if (!Salary::is_valid_password($request->salary_id)) {
                $pass = false;
            }
        }

        if ($pass) {
            return $next($request);
        } else {
            //无效
            if ($request->ajax()) {
                return responseToJson(-110, '查看工资条密码无效');
            } else {
                return '查看工资条密码无效';
            }
        }
    }
}
