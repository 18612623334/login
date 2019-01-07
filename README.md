laravel5.5 Component_login
### 通过Composer安装包。
#### API
从终端运行Composer require命令：
```
composer require wangliang/test-for-laravel:^v1.1
```
在config/app    providers数组中添加一个新行：
Wangliang\Test\TestServiceProvider::class

从终端运行发布服务 命令：
```
php artisan vendor:publish --  
```
运行数据库迁移
```
php artisan migrate
```

#### passport认证
```
composer require laravel/passport=~4.0
```
或
```
"laravel/passport": "~4.0",  （如果报错：在最下方加入"minimum-stability": "dev","prefer-stable": true）
```
执行数据迁移
php artisan migrate

官方文件
可以在Laravel网站上找到[Passport](https://laravel-china.org/docs/laravel/5.5/passport/1309)的文档

接下来，运行 php artisan passport:keys 命令来创建生成安全访问令牌时所需的加密密钥：
php artisan passport:keys

获取客户端的 ID和秘钥
php artisan passport:client

配置  .env  
```
PASSPORT_CLIENT_ID=2
PASSPORT_SECRET=秘钥
PASSPORT_GRANT_TYPE_PASSWORT=password  授权类型：密码授权
PASSPORT_GRANT_TYPE_REFRESH_TOKEN=refresh_token  
```
配置文件 config/auth.php 
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
#### Admin后台
配置后台登录图片验证码：
```
"mews/captcha": "^2.2",(这个百度一下  都有)
```
##### 配置后台Auth:
将配置文件 config/auth.php 
```
guards：新增
'admin' => [
     'driver' => 'session',
     'provider' => 'admins',
],
 providers：新增：
 'admins' => [
     'driver' => 'eloquent',
     'model' => App\Models\Admin\Admin::class,
 ],
 ```
 运行数据填充：
 ```
 php artisan db:seed --class=AdminTableSeeder
 ```
后台账号密码：账号（admin）密码（123456）
OK 按照步骤走下来  项目已经基本上跑通  如果跑不通  去百度上  好好学习一下
