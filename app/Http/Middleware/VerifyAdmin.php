<?php
/**
 * Created by PhpStorm.
 * User: hsingyue
 * Date: 2018/8/20
 * Time: 14:07
 */

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Permission;

class VerifyAdmin
{
    public function handle($request, Closure $next)
    {
        $route = Route::currentRouteName();

        if(!Auth::user()->hasVerifiedEmail()){
            if($route != "admin.index.authvalidate"){
                return redirect('/'.config('webset.web_indexname').'/validate_email')->withErrors('您没有该操作权限！');
            }else{
                return $next($request);
            }
        }

        $arr = explode('.',$route);
        $arr[count($arr)-1] = $arr[count($arr)-1] == "store" ?'create':$arr[count($arr)-1];
        $arr[count($arr)-1] = $arr[count($arr)-1] == "update" ?'edit':$arr[count($arr)-1];
        $route = implode('.',$arr);

        if (Auth::user()->hasPermissionTo($route)) {
            $perm = Permission::where('name',$route)->first();
            $perm->parent = Permission::find($perm->pid);
            $perm = $perm -> toArray();
            view()->share('perm',$perm);
            return $next($request);
        }

        $previousUrl = URL::previous();
        return redirect($previousUrl)->withErrors('您没有该操作权限！');
    }
}