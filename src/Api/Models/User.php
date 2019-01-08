<?php

namespace App\Models\Api;

use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use  Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'updated_at'
    ];

    /**
     * 根据手机号码 判断用户是否存在
     */
    public static function getUserPhone($phone)
    {
        $res = self::where('phone', $phone)->first();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据手机号码 获取用户密码
     */
    public static function getUserPassword($phone)
    {
        return self::where('phone', $phone)->select('password')->first();
    }

    /**
     * 记录用户登录错误次数和时间
     */
    public static function loginError($phone)
    {
        $res = self::where('phone', $phone)->select('login_error_number', 'login_error_date')->first();
        if($res){
            $res = $res->toArray();
        }
        if ($res) {
            $data['login_error_number'] = $res['login_error_number'] + 1;
            $data['login_error_date'] = date('Y-m-d H:i:s', time());
            self::where(['phone' => $phone])->update($data);
        }
    }

    /**
     * 用户登录成功后 更新错误记录
     */
    public static function loginSuccess($phone)
    {
        $data['login_error_number'] = 0;
        $data['login_error_date'] = '0000-00-00 00:00:00';
        self::where(['phone' => $phone])->update($data);
    }

    /**
     * 验证该手机号是否被锁定
     */
    public static function getUserPhoneLock($phone)
    {
        $res = self::where('phone', $phone)->select('login_error_number', 'login_error_date')->first();
        if ($res) {
            $res = $res->toArray();
        } else {
            return false;
        }
        //锁定次数
        $error_number = Config::get('constants.ERROR_NUMBER');
        //锁定时间
        $error_time = Config::get('constants.ERROR_TIME');

        if ($res['login_error_number'] >= $error_number && time() < (strtotime($res['login_error_date']) + $error_time)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getUserToken($token)
    {
        $res = self::where('token', $token)->select('id')->first();
        if ($res) {
            $res = $res->toArray();
            return $res['id'];
        } else {
            return false;
        }
    }
}
