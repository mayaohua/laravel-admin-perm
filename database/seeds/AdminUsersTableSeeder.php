<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_users')->insert([
            'name' => config('admin.sup.name'),
            'email' => config('admin.sup.email'),
            'password' => bcrypt(config('admin.sup.password')),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('admin_users')->insert([
            'name' => 'root',
            'email' => 'root@root.com',
            'password' => bcrypt('root'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
