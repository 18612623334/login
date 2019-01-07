<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Admin;
use App\Models\Admin\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    //判断用户是否登录 登录跳转首页 未登录跳转登录页面
    public function index(Request $request)
    {
        $id = session()->has('id');

        if ($id) {
            return redirect()->route("index.index");
        }

        return view("Admin.Login.login");
    }
    
    //登录
    public function login(LoginRequest $request)
    {
        $data = $request->only(['account', 'password']);

        $user = Admin::getAdminFromAccount($data['account']);

        if (Auth::guard('admin')->attempt($data)) {

            session(['id' => $user['id'], 'username' => $user['username']]);

            $this->urlGroup($user['id']);

            return response(['status' => 1, '登陆成功']);
        } else {
            return ['status' => '0', 'msg' => '账号或密码错误'];
        }
    }

    //获取用户组的导航
    public function urlGroup($user_id)
    {
        $admin_group = Admin::AdminUserGroupLis($user_id);

        $rule_all = Rule::AdminRuleRoute();

        $super_aid = Config::get('constants.SUPER_ADMIN_ID');
        $is_super_user = 0;
        if ($super_aid == $user_id) {
            $is_super_user = '1';
        }

        $admin_array_rule = [];
        if ($is_super_user == '1') {
            foreach ($rule_all as $k => $v) {
                $url='http://'.$_SERVER['HTTP_HOST'].'/'.$v['url'];
                $array=get_headers($url,1);
                if(!isset($array['1'])){
                    $v['status'] = 0;
                }else{
                    $v['status'] = 1;
                }
                $v['http'] = 'http://'.$_SERVER['HTTP_HOST'].'/';
                $v['url'] = str_replace('/', '.', $v['url']);
                $admin_array_rule[$v['navigation_name']][] = $v;
            }
        } else {
            foreach ($admin_group as $key => $value) {
                foreach ($rule_all as $k => $v) {
                    if ($value['naviagtion_id'] == $v['naviagtion_id'] && $value['id'] == $v['id']) {
                        $v['url'] = str_replace('/', '.', $v['url']);
                        $admin_array_rule[$v['navigation_name']][] = $v;
                    }
                }
            }
        }

        session(['navigation' => $admin_array_rule]);
    }
    
    //退出
    public function loginout()
    {
        @session(['id' => null, 'username' => null, 'navigation' => null, 'rule_all' => null, 'user_rule_group' => null]);

        return redirect()->route("login.index");
    }

}