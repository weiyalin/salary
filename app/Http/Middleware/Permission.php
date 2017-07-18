<?php

namespace App\Http\Middleware;

use Closure;

class Permission
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
        $uri = $request->path();
        if(!$this->has_permission($uri)){
            if ($request->ajax()) {
                return response('Forbidden. （超过权限，禁止访问）', 403);
            } else {
                return redirect()->guest('/login');
            }
        }
        return $next($request);
    }

    private function has_permission($uri){
        $permission = session('permission');
        $has_permission = false;

        if($permission){
            foreach($permission as $path){
                if(trim($path,'/') == $uri){
                    $has_permission = true;
                    break;
                }
            }
        }

        return $has_permission;
    }
}
