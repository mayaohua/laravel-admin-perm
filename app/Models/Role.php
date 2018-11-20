<?php
/**
 * Created by PhpStorm.
 * User: mayh
 * Date: 18/11/19
 * Time: 下午12:51
 */

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use \Spatie\Permission\Models\Role as BaeRole;

class Role extends BaeRole
{

    public static function getListByRole($roleArr,$limit){
        //获取角色列表
        $list = Role::whereNotIn('name',$roleArr)->paginate($limit);
        //获取角色列表下的各权限
        foreach ($list->items() as $key => $value){
            $list->items()[$key]->permissions = $value-> permissions()->where('level',1)->get(['show_name','name'])->toArray();
        }
        return $list;
    }

    public static function add( $data,$perms)
    {
        self::getPremsPrents($perms);
        try{
            DB::beginTransaction();
            //角色入库操作
            $data['guard_name'] = 'web';
            $data['name'] = ucwords($data['name']);
            $role =parent::create($data);
            // 权限入库
            $role->givePermissionTo($perms);
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
    }

    public static function edit($id,$data,$perms)
    {
        self::getPremsPrents($perms);
        $role = parent::findOrFail($id);
        try{
            DB::beginTransaction();
            //角色入库操作
            $data['guard_name'] = 'web';
            $data['name'] = ucwords($data['name']);
            $role -> update($data);
            // 权限入库
            $role -> syncPermissions($perms);
            DB::commit();
            return true;
        }catch (\Exception $e){
            dd($e);
            DB::rollBack();
            return false;
        }
    }

    /**
     * 删除
     * @param array|\Illuminate\Support\Collection|int $ids
     * @return int|void
     */
    public static function del($ids)
    {
        DB::beginTransaction();
        try{
            $roles = parent::whereIn('id',explode(',',$ids))->get();
            foreach ($roles as $role){
                if($role->name == 'Admin' || $role->name == 'SupAdmin'){
                    throw new \Exception('不能删除管理员角色',10001);
                }
                //用户删除角色
                foreach (AdminUser::all() as $user){
                    $user->removeRole($role->name);
                }
                $role->delete();
            }
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
    }


    /**
     * 获取权限树
     * @return array
     */
    public static function getPermsTree(){
        $perms = parent::findByName('Admin')->getAllPermissions();
        return self::getPermsCallback($perms);
    }


    private static function getPermsCallback($perms,$pid=0){
        $resultArr = [];
        foreach ($perms as $key => $perm){
            if($perm->pid == $pid){
                $arr = [
                    'id' => $perm -> id,
                    'label'=> $perm -> show_name,
                    'children' => self::getPermsCallback($perms,$perm -> id)
                ];
                $resultArr[] = $arr;
            }
        }
        return $resultArr;
    }


    private static function getPremsPrents(&$perms){
        foreach ($perms as $permId){
            $perm = Permission::find($permId);
            if($perm->pid != 0){
                if(!in_array($perm->pid,$perms)){
                    $perms[] = $perm->pid;
                    self::getPremsPrents($perms);
                }

            }
        }
    }

    public static function removePremsPrents(&$perms){
        foreach ($perms as $key => $permId){
            $perm = Permission::find($permId);
            $index = array_search($perm->pid,$perms);
            if($index !== false){
                array_splice($perms,$index,1);
            }
        }
        //dd($perms);
    }
}