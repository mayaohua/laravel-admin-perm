<?php

Auth::routes();

Route::group(['middleware' => 'auth','as' => 'admin.'],function($route){

    Route::group(['middleware' => 'admin'],function($route){

        Route::get('/', 'HomeController@index')->name('index');
        Route::get('/index', 'HomeController@index')->name('index');

        Route::get('password', 'HomeController@show_password_view')->name('index.password'); //显示修改密码界面
        Route::post('password/form', 'HomeController@form_password')->name('index.password'); //表单方式修改密码
        Route::post('password/email', 'HomeController@email_password')->name('index.password'); //邮箱验证方式修改密码

        Route::any('validate_email', 'HomeController@email_validate')->name('index.authvalidate');//邮箱身份验证界面级操作


        Route::resource('role', 'RoleController');
        Route::resource('adminuser', 'AdminUserController');

        $route->get('/pas',function(){
            return 'welcome';
        })->name('admin.pas.show');

    });

});

