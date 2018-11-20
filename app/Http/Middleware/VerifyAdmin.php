<?php
/**
 * Created by PhpStorm.
 * User: hsingyue
 * Date: 2018/8/20
 * Time: 14:07
 */

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class VerifyAdmin
{
    public function handle($request, Closure $next)
    {
        $route = Route::currentRouteName();

        $arr = explode('.',$route);
        $arr[count($arr)-1] = $arr[count($arr)-1] == "store" ?'create':$arr[count($arr)-1];
        $arr[count($arr)-1] = $arr[count($arr)-1] == "update" ?'edit':$arr[count($arr)-1];
        $route = implode('.',$arr);
        if (\Gate::check($route)) {
            $perm = Permission::where('name',$route)->first();
            $perm->parent = Permission::find($perm->pid);
            $perm = $perm -> toArray();
            view()->share('perm',$perm);
            return $next($request);
        }
        return redirect('admin/index')->withErrors('您没有该操作权限！');
    }
}