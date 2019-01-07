<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use Flc\Dysms\Client;
use Flc\Dysms\Request\SendSms;

class Sms extends Model
{

    protected $table = 'sms';

    protected $primaryKey = "id";


    //短信参数
    protected static $config = [
        'accessKeyId' => 'LTAIziidheqC6jdg',
        'accessKeySecret' => 'LWmYTV27wdA7i4L29tsQnms7RzziUM',
    ];

    //验证手机号码一小时发送次数
    public static function getPhoneNum($phone)
    {
        return self::where(["phone" => $phone])->whereBetween('created_at', [date('Y-m-d H:i:s', time() - 3600), date('Y-m-d H:i:s', time())])->count();
    }

    //验证短信验证码
    public static function checkCode($phone, $sms_code)
    {
        $data = self::where(["phone" => $phone, "sms_code" => $sms_code])->orderBy("id", "desc")->get(["id", "created_at"])->first();

        if (is_null($data)) {
            return false;
        } else {
            return $data->toArray();
        }
    }

    //发送短信
    public static function sendSms($code, $phone)
    {
        $config = self::$config;

        $client = new Client($config);
        $sendSms = new SendSms;
        $sendSms->setPhoneNumbers($phone);
        $sendSms->setSignName(''); //签名 名称
        $sendSms->setTemplateCode(''); //签名 模版

        $sendSms->setTemplateParam(['code' => $code]);

        $rs = $client->execute($sendSms);

        if ($rs->Code == 'OK') {
            return 1;
        } else {
            return $rs->Message;
        }
    }

}
