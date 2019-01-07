<?php

namespace Wangliang\Test;

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
        
        /*
         * Admin
         */
        $this->publishes([
            realpath(__DIR__ . '/Admin/Admin') => base_path('app/Http/Controllers/Admin'),
        ]);

        //模型
        $this->publishes([
            realpath(__DIR__ . '/Admin/Models') => base_path('app/Models/Admin'),
        ]);

        //路由
        $this->publishes([
            realpath(__DIR__ . '/Admin/Routes') => base_path('routes/Admin'),
        ]);

        //验证类
        $this->publishes([
            realpath(__DIR__ . '/Admin/Requests') => base_path('app/Http/Requests/Admin'),
        ]);

        //配置文件
        $this->publishes([
            __DIR__.'/config/constants.php' => config_path('constants.php'),
        ]);

        //迁移文件
        $this->publishes([
            realpath(__DIR__ . '/database/migrations') => base_path('/database/migrations'),
        ]);

        //填充文件
        $this->publishes([
            realpath(__DIR__ . '/database/seeds') => base_path('/database/seeds'),
        ]);
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('test', function ($app) {
            return new test();
        });
    }
}
