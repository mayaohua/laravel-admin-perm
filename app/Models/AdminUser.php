<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;

class AdminUser extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];



    public static function getList($limit)
    {
        $users = parent::where('email','!=',config('admin.sup.email'))->paginate($limit);
        foreach ($users->items() as $key => $user){

            $role = $user->roles->first();
            $data = [
                'id' => $role->id,
                'show_name' => $role->show_name,
            ];
            $prems = $role->permissions()->where('level',1)->get(['show_name'])->toArray();
            $data['prems'] = join(',',array_column($prems, 'show_name'));

            $users->items()[$key]['role'] = $data;

            unset($user->roles);
        }
        return $users;
    }


    public static function add($data,$role)
    {
        try{
            DB::beginTransaction();
            $data ['password'] = Hash::make(config('admin.userpwd'));

            $user =parent::create($data);
            // 权限入库
            $user->assignRole($role);
            //公共权限
            $user->givePermissionTo(['admin.index','admin.index.caiwu','admin.index.renyuan']);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getCode();
        }
    }

    public static function edit($id,$data,$role)
    {
        try{
            DB::beginTransaction();
            $user = parent::findOrFail($id);
            $user -> update($data);
            // 权限入库
            $user->syncRoles($role);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getCode();
        }
    }

    /**
     * 删除
     * @param array|\Illuminate\Support\Collection|int $ids
     * @return int|void
     */
    public static function del($ids)
    {
        try{
            DB::beginTransaction();
            $users = parent::whereIn('id',explode(',',$ids))->get();
            foreach ($users as $user){
                if($user->email == config('admin.sup.email')){
                    throw new \Exception('不能删除超级管理员',10001);
                }
                //删除直接赋予的权限
                $user->syncPermissions([]);
                //删除
                $user->delete();
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return $e->getCode();
        }
    }
}
