<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\AdminUser;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 重置角色和权限缓存
        app()['cache']->forget('spatie.permission.cache');

        // 创建后台人员权限
        $node = Permission::create(['name' => 'admin.adminuser', 'show_name'=>'管理员管理', 'icon'=> 'fas fa-user-secret',  'level' => 1]);
        $child = Permission::create(['name'=>'admin.adminuser.index','show_name' => '管理员列表', 'pid' => $node['id'], 'level' => 2]);

        Permission::create(['name'=>'admin.adminuser.create','show_name' => '添加管理员', 'pid' => $node['id'], 'level' => 2]);

        Permission::create(['name'=>'admin.adminuser.edit', 'show_name' => '编辑管理员','pid' => $child['id'], 'level' => 3]);
        Permission::create(['name'=>'admin.adminuser.destroy','show_name' => '删除管理员', 'pid' => $child['id'], 'level' => 3]);
        Permission::create(['name'=>'admin.adminuser.show', 'show_name' => '查看管理员','pid' => $child['id'], 'level' => 3]);

        // 创建角色权限
        $node = Permission::create(['name' => 'admin.role', 'show_name'=>'角色管理', 'icon'=> 'fas fa-arrows-alt',  'level' => 1]);
        $child = Permission::create(['name'=>'admin.role.index','show_name' => '角色列表', 'pid' => $node['id'], 'level' => 2]);

        Permission::create(['name'=>'admin.role.create','show_name' => '添加角色', 'pid' => $node['id'], 'level' => 2]);

        Permission::create(['name'=>'admin.role.edit', 'show_name' => '编辑角色','pid' => $child['id'], 'level' => 3]);
        Permission::create(['name'=>'admin.role.destroy','show_name' => '删除角色', 'pid' => $child['id'], 'level' => 3]);
        Permission::create(['name'=>'admin.role.show', 'show_name' => '查看角色','pid' => $child['id'], 'level' => 3]);



        //管理员角色
        $role = Role::create(['name' => 'Admin','show_name'=>'管理员']);
        $role->givePermissionTo([
            'admin.role','admin.role.index','admin.role.create','admin.role.edit','admin.role.destroy','admin.role.show',
            'admin.adminuser','admin.adminuser.index','admin.adminuser.create','admin.adminuser.edit','admin.adminuser.destroy','admin.adminuser.show',
        ]);

        //管理员赋予角色
        $user = AdminUser::where('name','root')->first();
        //管理员分配角色
        $user->assignRole('Admin');




        // 创建超级管理员角色
        $role = Role::create(['name' => 'SupAdmin','show_name'=>'超级管理员']);
        // 给角色分配创建的权限
        $role->givePermissionTo(Permission::all());
        //超级管理员赋予角色
        $user = AdminUser::where('email',config('admin.sup.email'))->first();
        //超级管理员分配角色
        $user->assignRole('SupAdmin');
        //超级管理员分配权限／行为能力
        //$user->givePermissionTo(Permission::all());



        ///分派公共的权限
        //控制台
        $node = Permission::create(['show_name' => '控制台','icon' => 'fas fa-cog', 'name'=>'admin.index', 'pid' => 0, 'level' => 1]);
        Permission::create(['show_name' => '修改密码','name'=>'admin.index.password', 'pid' => $node['id'], 'level' => 2]);
        Permission::create(['show_name' => '身份验证','name'=>'admin.index.authvalidate', 'pid' => $node['id'], 'level' => 3]);    //不需要放入公共权限
        Permission::create(['show_name' => '财务数据','name'=>'admin.index.caiwu', 'pid' => $node['id'], 'level' => 3]);
        Permission::create(['show_name' => '人员数据','name'=>'admin.index.renyuan', 'pid' => $node['id'], 'level' => 3]);

        foreach (AdminUser::all() as $user){
            $user->givePermissionTo(['admin.index','admin.index.caiwu','admin.index.renyuan','admin.index.password']);
        }


    }
}
