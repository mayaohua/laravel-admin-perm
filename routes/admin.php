<?php

Auth::routes();

Route::group(['middleware' => 'auth','as' => 'admin.'],function($route){

    Route::group(['middleware' => 'admin'],function($route){

        Route::get('/', 'HomeController@index')->name('index');
        Route::get('/index', 'HomeController@index')->name('index');

        Route::resource('role', 'RoleController');
        Route::resource('adminuser', 'AdminUserController');

        $route->get('/pas',function(){
            return 'welcome';
        })->name('admin.pas.show');

    });

});

