<?php

namespace App\Models\Api;

use Illuminate\Support\Facades\Config;
use Sts\Request\V20150401 as Sts;

class Authentication{

public static function getData($unique)
{
    require_once base_path().'/public/sdk/aliyun-php-sdk-core/Config.php';

    $appkey = config('alioss.ALIOSS_KEYID');
    $appsecret = config('alioss.ALIOSS_KEYSECRET');

    // 你需要操作的资源所在的region，STS服务目前只有杭州节点可以签发Token，签发出的Token在所有Region都可用
    // 只允许子用户使用角色
    $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $appkey, $appsecret);
    $client = new \DefaultAcsClient($iClientProfile);

    // 角色资源描述符，在RAM的控制台的资源详情页上可以获取
    $roleArn = "acs:ram::1129536726282309:role/xh";
    
$policy=<<<POLICY
{
  "Statement": [
    {
      "Action": "oss:*",
      "Effect": "Allow",
      "Resource": "*"
    }
  ],
  "Version": "1"
}
POLICY;

    $request = new Sts\AssumeRoleRequest();
    $request->setRoleSessionName($unique);
    $request->setRoleArn($roleArn);
    $request->setPolicy($policy);
    $request->setDurationSeconds(900);
    $response = $client->doAction($request);

    if($response->isSuccess()){
        $data = json_decode(($response->getBody()),true);
        $data['status'] = 1;
    }else{
        $data = ['status'=>0];
    }

    return $data;
}

}