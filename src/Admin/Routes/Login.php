<?php

use Illuminate\Support\Facades\Config;

Route::middleware('admin')->domain(Config::get('constants.ADMIN_URL'))->namespace('Admin')->group(function () {

    //登录页
    Route::get("/login/index",[
        'as'=>"login.index",'uses' => 'LoginController@index',
    ]);

    //提交登录
    Route::post("/login/login",[
        'as'=>"login.login",'uses' => 'LoginController@login',
    ]);

    //登录页
    Route::get("/",[
        'as'=>"login.index",'uses' => 'LoginController@index',
    ]);

    //退出登录
    Route::get("/login/loginout",[
        'as'=>"login.loginout",'uses' => 'LoginController@loginout',
    ]);

    //403权限不足
    Route::get("/index/authError",[
        'as'=>"index.authError",'uses' => 'IndexController@authError',
    ]);


});