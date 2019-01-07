<?php

use Illuminate\Support\Facades\Config;

Route::middleware('admin')->domain(Config::get('constants.ADMIN_URL'))->namespace('Admin')->group(function () {

    //首页
    Route::get("/index/index",[
        'as'=>"index.index",'uses' => 'IndexController@index',
    ]);

    //欢迎页面
    Route::get("/index/welcome",[
        'as'=>"index.welcome",'uses' => 'IndexController@welcome',
    ]);

    Route::post('/index/get-echart' , 'IndexController@getEchart')->name('index.get-echart');
    
    
});