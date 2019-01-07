<?php

namespace App\Models\Admin;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AuthAdmin extends Base
{

    public static function checkAdminRule($admin,$rule)
    {
        $super_aid = Config::get('constants.SUPER_ADMIN_ID');

        if ($super_aid == $admin) {

            return true;
        }

        //验证地址是否存在
        $all_rule = self::ruleAll();

        //获取用户所拥有的权限

        $user_rule = self::userRule($admin);

        //路由地址错误
        if (!in_array($rule,$all_rule)) {

            return false;

        }

        if (in_array($rule,$user_rule)) {

            return true;

        }

        return false;

    }

    public static function ruleAll()
    {

        $has_session_rule_all = session('rule_all');

        if ($has_session_rule_all) {

            $list = session('rule_all');

        } else {

            $list = Rule::ruleAll();

            session(['rule_all' => $list]);

        }

        return $list;

    }

    public static function userRule($admin)
    {

        //用户所属用户组
        $user_rule = session('user_rule_group');
        if ($user_rule) {

            $user_rule = session('user_rule_group');

        } else{

            $user_rule = self::userRuleGroup($admin);

            session(['user_rule_group'=>$user_rule]);

        }

        return $user_rule;

    }

    public static function userRuleGroup($admin)
    {

        $user_group = Group::userGroup($admin);
   
        if ($user_group) {

            //获取所有的路由信息
            $user_rule = Group::userRule($user_group['group_id']);

            $user_rule = array_unique($user_rule);

            if ($user_rule) {

                $user_rule_data = Rule::userRuleData($user_rule);

                $user_rule_data = array_unique($user_rule_data);

            }
            return $user_rule_data;
        }



    }

}