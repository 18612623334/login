<?php

namespace App\Http\Controllers\Api;

use App\Models\Api\Jpush;
use App\Models\Api\Oss;

class PublicController extends BaseController
{


    /*
     * 测试 极光推送(demo)
     */
    public function test()
    {
        $token = $this->getInput('token');
        $user_id = User::getUid($token);

        if (!$user_id) {
            return $this->returnMsg('-2', '未登录');
        }

        $message = $this->getInput('message');

        Jpush::pushMsg($user_id, $message);
    }


    /*
     * OSS 移动端获取临时TOKEN
     * 文件上传获取token
     * 鉴权模式用
    */
    public function getAuthenticationToken()
    {
        $unique = $this->getInput('unique');

        $data = Oss::getData($unique);

        if ($data['status']) {
            $return = [
                'appkey' => $data['Credentials']['AccessKeyId'],
                'appsecret' => $data['Credentials']['AccessKeySecret'],
                'lasttime' => (string)strtotime($data['Credentials']['Expiration']),
                'token' => $data['Credentials']['SecurityToken'],
            ];
            return response()->json(['status' => '1', 'msg' => '成功', 'data' => $return]);
        } else {
            return response()->json(['status' => '0', 'msg' => '失败，稍后重试']);
        }
    }

}