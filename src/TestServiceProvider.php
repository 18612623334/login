<?php

namespace Wangliang\Login;

use Illuminate\Support\ServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * API
         */  
        $this->publishes([
            realpath(__DIR__ . '/Api/Api') => base_path('app/Http/Controllers/Api'),
        ]);

        //模型
        $this->publishes([
            realpath(__DIR__ . '/Api/Models') => base_path('app/Models/Api'),
        ]);

        //路由
        $this->publishes([
            realpath(__DIR__ . '/Api/Routes') => base_path('routes/Api'),
        ]);

        //验证类
        $this->publishes([
            realpath(__DIR__ . '/Api/Requests') => base_path('app/Http/Requests/Api'),
        ]);
        
        //配置文件
        if(file_exists(config_path('constants.php'))==false){
            $this->publishes([
                __DIR__.'/config/constants.php' => config_path('constants.php'),
            ]);
        }

        //迁移文件
        $this->publishes([
            realpath(__DIR__ . '/database/migrations') => base_path('/database/migrations'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('login', function ($app) {
            return new login();
        });
    }
}
