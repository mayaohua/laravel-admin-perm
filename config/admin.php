<?php

return [
   'sup' => [
       'email' => env('ADMIN_EMAIL', 'admin@163.com'),   //超级管理员邮箱
       'name' =>  env('ADMIN_NAME', 'supAdmin'),   //超级管理员姓名
       'password' =>  env('ADMIN_PASSWORD', 'admin'),   //超级管理员密码
   ],

   'userpwd' => env('ADMIN_USERPWD', '123456'),    //后台人员默认密码

];