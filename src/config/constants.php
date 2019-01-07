<?php


return [
    //后台地址
    'ADMIN_URL' => 'admin.ibangoo.com',

    //APP地址
    'APP_URL' => 'basis.ibangoo.com',

    //WEB地址
    'WEB_URL' => 'web.ibangoo.com',
    
    'SUPER_ADMIN_ID' => 1,

    //新用户的默认头像
    'DEFAULT_HEADER' => 'http://bitimg.ibangoo.com/2018/03/13/f3c01ac7102afd71675f7b3558a58944.png',
    
    //锁定次数 
    'ERROR_NUMBER' => 5,
    
    //解锁时间
    'ERROR_TIME' => 1200,

    //支付宝
    'ALI_PAY_PARAMS'=>[
        'app_id'=>'',
        'app_private_key'=>'',
        'alipay_public_key'=>'',
    ],
    //微信
    'WX_PAY_PARAMS'=>[
        'app_id'=>'',
        'mch_id'=>'',
        'api_key'=>'',
        'api_secret'=>'',
    ],


    //微信扫描登录(WEB)
    'APP_ID' => env('APP_ID',''), ////微信开放平台APPID
    'SECRET' => env('SECRET',''), //微信开放平台密钥
    'REDIRECT_URL' => env('REDIRECT_URL',''), //微信开放平台回调地址
    'RESPONSE_TYPE' => env('RESPONSE_TYPE','code'), //参数
    'SCOPE' => env('SCOPE','snsapi_login'), //作用域


    //passport 认证(API)
    'PASSPORT_CLIENT_ID' => env('PASSPORT_CLIENT_ID', ''),
    'PASSPORT_SECRET' => env('PASSPORT_SECRET', ''),
    'PASSPORT_GRANT_TYPE_PASSWORT' => env('PASSPORT_GRANT_TYPE_PASSWORT', ''),
    'PASSPORT_GRANT_TYPE_REFRESH_TOKEN' => env('refresh_token', ''),

    //极光推送(API)
    'JPUSH_APP_KEY' =>env('JPUSH_APP_KEY'), //极光APP_KEY
    'JPUSH_MASTER_SECRET' =>env('JPUSH_MASTER_SECRET'), //极光秘钥
    'JPUSH_LINE' => 0,  //IOS 上架后改为1
    'JPUSH_APP_NAME'=>env('JPUSH_APP_NAME'), //APP名

    //
];

