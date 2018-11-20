<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->increments('id')->comment('权限表自增ID');
            $table->string('name')->comment('权限名称');
            $table->string('show_name')->comment('权限显示名称');
            $table->string('guard_name')->comment('权限分组');
            $table->string('icon')->comment('权限图标')->nullable();
//            $table->integer('is_hide')->comment('是否隐藏 1=隐藏 0=显示')->default(0);
            $table->integer('pid')->comment('父ID')->default(0);
            $table->integer('level')->comment('菜单等级')->default(0);
            $table->timestamps();
        });

        DB::statement('alter table '.env('DB_PREFIX','').'permissions comment "权限表"');

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->increments('id')->comment('角色表自增ID');
            $table->string('name')->comment('角色名称');
            $table->string('show_name')->comment('角色显示名称');
            $table->string('guard_name')->comment('角色分组');
            $table->timestamps();
        });

        DB::statement('alter table '.env('DB_PREFIX','').$tableNames['roles']. ' comment "角色表"');

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedInteger('permission_id');
            $table->morphs('model');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        DB::statement('alter table '.env('DB_PREFIX','').$tableNames['model_has_permissions']. ' comment "用户权限关联表"');

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedInteger('role_id');
            $table->morphs('model');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        DB::statement('alter table '.env('DB_PREFIX','').$tableNames['model_has_roles']. ' comment "用户角色关联表"');

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedInteger('permission_id');
            $table->unsignedInteger('role_id');

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);

            app('cache')->forget('spatie.permission.cache');
        });

        DB::statement('alter table '.env('DB_PREFIX','').$tableNames['role_has_permissions']. ' comment "角色权限关联表"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}