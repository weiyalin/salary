<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin
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
        if(empty(get_user_info())){
            if ($request->ajax()) {
                return response("Unauthorized.（未登录）", 401)->header("X-CSRF-TOKEN", csrf_token());
            } else {
                return redirect('/login');
            }
        }

        return $next($request);
    }
}
