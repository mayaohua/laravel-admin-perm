<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoleAddRequest;
use App\Http\Requests\Admin\RoleEditRequest;
use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        $limArr = parent::getLimits();
        $limits = $limArr[0];
        $limit = $limArr[1];

        $roleArr = ['SupAdmin'];

        if(Auth::user()->email != config('admin.sup.email')){
            array_push($roleArr,'Admin');
        }
        $list = Role::getListByRole($roleArr,$limit);

        return view('admin.role.index', compact(['list','limit','limits']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $perms = Role::getPermsTree();
        return view('admin.role.create',compact('perms'));
    }

    /**
     * @param RoleAddRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(RoleAddRequest $request)
    {
        $data = $request -> only(['show_name','name']);
        $perms = $request->get('permissions');
        if(Role::add($data,$perms)){
            return $this -> success();
        }
        return $this -> fail(500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $info = Role::where('id',$id)->get(['id','name','show_name'])->first();

        if(!$this->hasEditPower($info)){
            return redirect()->back()->withErrors('您没有该操作权限！');;
        }

        $info->permissions()->select(['id'])->get();
        $perms = [];
        foreach ($info->permissions as $permission){
            array_push($perms,$permission->id);
        }
        $info = $info->toArray();
        Role::removePremsPrents($perms);
        $info['permissions'] = $perms;
        $perms = Role::getPermsTree();
        return view('admin.role.edit',compact('perms','info','id'));
    }

    /**
     * 修改操作
     * @param Request $request
     * @param $id
     */
    public function update(RoleEditRequest $request, $id)
    {
        $info = Role::find($id);
        if(!$this->hasEditPower($info)){
            return redirect()->back()->withErrors('您没有该操作权限！');;
        }

        $data = $request -> only(['show_name','name']);
        $perms = $request->get('permissions');
        if(Role::edit($id,$data,$perms)){
            return $this -> success();
        }

        return $this -> fail(500);
    }

    /**
     * 删除一条或多条
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        if(Role::del($id)){
            return $this->success();
        }else{
            return $this->fail(500);
        }
    }

    protected function hasEditPower($info){
        //不能修改超级管理员角色
        if($info->name == 'SupAdmin'){
            return false;
        }
        //其他人不能修改管理员角色
        if(\auth()->user()->email != config('admin.sup.email') && $info->name == 'Admin'){
            return false;
        }
        return true;
    }

}
