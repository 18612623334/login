<?php

namespace App\Model\App;

use JPush\Client as JPushs;
use Illuminate\Support\Facades\Config as Configs;
use Illuminate\Support\Facades\DB;

class Jpush extends Base
{


    public static function pushMsg($user_id, $message)
    {
        $app_key = Configs::get('constants.JPUSH_APP_KEY');

        $app_secret = Configs::get('constants.JPUSH_MASTER_SECRET');

        //获取用户的极光设备ID
        $user = DB::table('user')->where(['id' => $user_id])->select('type', 'jpush_id')->first();
        $user = self::objectToArray($user);

        $client = new JPushs($app_key, $app_secret, null);
        $push = $client->push();

        $push->setPlatform('all');

        if ($user) {
            $push->addRegistrationId($user['jpush_id']);
        } else {
            return false;
        }

        $title = Configs::get('constants.JPUSH_APP_NAME');
        $push->setNotificationAlert($title)
            ->iosNotification(['title' => $title, 'body' => $message], array(
                'sound' => '',
                'category' => 'jiguang',
                'extras' => array(
                    'content' => $message,
                ),
            ))
            ->androidNotification($message, array(
                'title' => $title,
                'extras' => array(
                    'content' => $message,
                ),
            ));

        $is_on_line = Configs::get('constants.JPUSH_LINE');

        if ($is_on_line == 1) {
            $push->options(['apns_production' => true]);
        }

        try {
            $push->send();
            return true;
        } catch (\JPush\Exceptions\JPushException $e) {
            return ($e);
        }

    }


}