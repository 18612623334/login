laravel5.5 Component_login
### 通过Composer安装包。
#### API
#### 从终端运行Composer update命令：
```
"wangliang/laravel-login":"^v1.3"
```
#### 在config/app  providers数组中添加一个新行：
```
Wangliang\Login\TestServiceProvider::class,
```
#### 从终端运行发布服务 命令：
```
php artisan vendor:publish --  
```
#### 运行数据库迁移
```
php artisan migrate (先删除框架自带的user数据迁移文件)(关掉laravel config/database 下的mysql 严格模式 strict:false)
```
#### 在 app/Providers/RouteServiceProvider 修改路由
##### mapApiRoutes(方法)
```
foreach (glob(base_path('routes/Api') . '/*.php') as $file) {
    Route::middleware('api')
        ->namespace($this->namespace)
        ->group($file);
}
```
#### 修改语言包文件resources\lang\en\validation.php
```
custom ：新增
'phone' =>[
    'required' => '请您输入手机号码',
    'regex' => '请输入正确的11位手机号码',
],

'password' =>[
    'required' => '请您输入密码',
    'between' => '请您输入6~15位密码',
    'regex' => '密码格式错误，请重新输入',
    'confirmed' => '两次输入密码不一致',
],

'sms_code' =>[
    'required' => '请您输入短信验证码',
    'regex' => '请输入正确的6位短信验证码',
],
```
#### passport认证
```
"laravel/passport": "~4.0",  （如果报错：在最下方加入"minimum-stability": "dev","prefer-stable": true）
```
#### 执行数据迁移
php artisan migrate

官方文件
可以在Laravel网站上找到[Passport](https://laravel-china.org/docs/laravel/5.5/passport/1309)的文档

#### 接下来，运行命令来创建生成安全访问令牌时所需的加密密钥(/storage/下  生成两个密钥)：
```
php artisan passport:keys
```
#### 接下来我们安装 Passport 以生成令牌和客户端
```
php artisan passport:install
```
#### 配置  .env  
```
PASSPORT_CLIENT_ID=2
PASSPORT_SECRET=秘钥
PASSPORT_GRANT_TYPE_PASSWORT=password  授权类型：密码授权
PASSPORT_GRANT_TYPE_REFRESH_TOKEN=refresh_token  
```
#### 配置文件 config/auth.php 
```
'api' => [
     'driver' => 'passport',
     'provider' => 'users',
],
'users' => [
     'driver' => 'eloquent',
     'model' => App\Models\Api\User::class,
],
```
中授权看守器 guards 的 api 的 driver 选项改为 passport

#### 修改passport 自带的邮箱登录 (email 改成 phone)
```
vendor/laravel/passport/src/Bridge/UserRepository.php
```
#### 接下来，你需要在 AuthServiceProvider 的 boot 方法中调用 Passport::routes 方法，该方法将会注册发布/撤销访问令牌、客户端以及私人访问令牌所必需的路由 
```
引用：use Laravel\Passport\Passport;
boot 方法添加：Passport::routes();
```

OK 按照步骤走下来  项目已经基本上跑通  如果跑不通  去百度上  好好学习一下
