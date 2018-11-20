<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminUserAddRequest;
use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminUserController extends Controller
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

        $list = AdminUser::getList($limit);

        return view('admin.adminuser.index',compact(['list','limit','limits']));
    }


    public function create()
    {
        $roleArr= ['SupAdmin'];
        $roles = Role::whereNotIn('name',$roleArr)->get(['id','show_name','name']);
        return view('admin.adminuser.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserAddRequest $request)
    {
        $data = $request->only('name','email');
        return $this->back(AdminUser::add($data,$request->get('role')));
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roleArr= ['SupAdmin'];
        $roles = Role::whereNotIn('name',$roleArr)->get(['id','show_name','name']);
        $info = AdminUser::find($id);
        $info->role = $info->roles->first()->id;
        //不能修改超级管理员资料
        if($info->email == config('admin.sup.email')){
            return redirect()->back()->withErrors('您没有该操作权限！');
        }
        return view('admin.adminuser.edit',compact('roles','info','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->only('name','email');
        //不能修改超级管理员资料
        if(AdminUser::find($id)->email == config('admin.sup.email')){
            return $this -> fail(403);
        }
        return $this->back(AdminUser::edit($id,$data,$request->get('role')));
    }

    /**
     *  删除一条或多条
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        return $this->back(AdminUser::del($id));
    }


}
