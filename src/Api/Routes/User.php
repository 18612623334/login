<?php

use Illuminate\Support\Facades\Config;

//不需要认证的接口
Route::middleware('api')->domain(Config::get('constants.APP_URL'))->prefix('api')->namespace('Api')->group(function () {

    //注册
    Route::post('/user/register', 'UserController@register')->name('user.register');

    //登录
    Route::post('/user/login', 'UserController@login')->name('user.login');

    //忘记密码
    Route::post('/user/forget-password', 'UserController@forgetPassword')->name('user.forgetPassword');

    //快捷登录
    Route::post('/user/quick-login', 'UserController@quickLogin')->name('user.quickLogin');

    //第三方登录
    Route::post('/user/tripartite-login', 'UserController@tripartiteLogin')->name('user.tripartiteLogin');

    //绑定手机号
    Route::post('/user/bind-phone', 'UserController@bindPhone')->name('user.bindPhone');

    //发送短信验证码
    Route::post('/user/send-sms', 'UserController@sendSms')->name('user.sendSms');

});

//需要认证的接口
Route::middleware('auth:api')->domain(Config::get('constants.APP_URL'))->prefix('api')->namespace('Api')->group(function () {

    //获取用户信息 （暂时未用） 
    Route::post('/user/get-user-info', 'UserController@getUserInfo')->name('user.get-user-info');

    //绑定第三方
    Route::post('/user/bind-tripartite', 'UserController@bindTripartite')->name('user.bindTripartite');

    //修改手机号码
    Route::post('/user/modify-phone', 'UserController@modifyPhone')->name('user.modifyPhone');

    //修改密码
    Route::post('/user/modify-password', 'UserController@modifyPassword')->name('user.modifyPassword');

    //退出
    Route::post('/user/logout', 'UserController@logout')->name('user.logout');
});

