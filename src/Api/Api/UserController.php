<?php

namespace App\Http\Controllers\Api;

use Validator;
use App\Models\Api\User;
use App\Models\Api\Sms;
use App\Models\Api\UserTripartite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\UserRequest;

class UserController extends BaseController
{

    
    public function username()
    {
        return 'phone';
    }
    /**
     * 注册
     * @access public
     * @param int $phone 手机号码
     * @param string $password 密码
     * @param int $sms_code 短信验证码
     * @return string
     */
    public function register(Request $request)
    {

        $data = $request->only(['phone', 'password', 'sms_code']);
        try {
            //规则验证
            $validate = Validator::make($data, UserRequest::rules('register'));
            if ($validate->fails()) {
                return response()->json(['status' => '0', 'msg' => $validate->messages()->first()]);
            }

            //逻辑验证
            $check_array = ['phone_already_exists'];
            $logic_verification = UserRequest::logicVerification($check_array, $data);
            if ($logic_verification) {
                return $logic_verification;
            }

            $user = new User();
            $user->phone = $data['phone'];
            $user->password = bcrypt($data['password']);
            $user->nickname = '用户_' . $this->getRandomString(6);
            $user->header = Config::get('constants.DEFAULT_HEADER');
            $user->save();

            $data = $this->sendLoginResponse($request);

            $data = json_decode($data->content(),true);

            // 注册的用户让其进行登陆状态
            $info = $this->authentication($user,array('access_token'=>$data['access_token']));

            return response()->json(['status' => '1', 'msg' => '注册成功', 'data' => $info]);

        } catch (\Exception $e) {
            return response()->json(['status' => '0', 'msg' => '注册失败']);
        }
    }

    /**
     * 登录
     * @access public
     * @param int $phone 手机号码
     * @param string $password 密码
     * @return string
     */
    
    public function login(Request $request)
    {
        $data = $request->only(['phone', 'password']);

        //规则验证
        $validate = Validator::make($data, UserRequest::rules('login'));
        if ($validate->fails()) {
            return response()->json(['status' => '0', 'msg' => $validate->messages()->first()]);
        }

        //逻辑验证
        $check_array = ['phone_not_exists', 'phone_lock'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        $credentials = $this->credentials($request);

        if ($this->guard('api')->attempt($credentials)) {

            $data = $this->sendLoginResponse($request);

            $data = json_decode($data->content(),true);

            $user = User::find(Auth::id());

            $info = $this->authentication($user,array('access_token'=>$data['access_token']));

            User::loginSuccess($request->phone);

            return response()->json(['status' => '1', 'msg' => '登录成功','data'=>$info]);            
        }else{
            User::loginError($request->phone);

            return response()->json(['status' => '0', 'msg' => '用户名或密码错误']);
        }
    }

    /**
     * 忘记密码
     * @access public
     * @param int $phone 手机号码
     * @param int $sms_code 短信验证码
     * @param string $password 密码
     * @param string $password_confirmation 确认密码
     * @return string
     */
    public function forgetPassword(Request $request)
    {
        $data = $request->only(['phone', 'sms_code', 'password', 'password_confirmation']);

        //规则验证
        $validate = Validator::make($data, UserRequest::rules('forget_password'));
        if ($validate->fails()) {
            return response()->json(['status' => '0', 'msg' => $validate->messages()->first()]);
        }

        //逻辑验证
        $check_array = ['phone_not_exists', 'twice_password_accordance', 'sms_code'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        $user = User::where('phone', $data['phone'])->first();
        $user->password = bcrypt($data['password']);
        $user->save();

        $data = $this->sendLoginResponse($request);

        $data = json_decode($data->content(),true);

        //修改密码后 登录
        $info = $this->authentication($user,array('access_token'=>$data['access_token']));

        return response()->json(['status' => '1', 'msg' => '修改成功', 'data' => $info]);
    }

    /**
     * 快捷登录
     * @access public
     * @param int $phone 手机号码
     * @param int $sms_code 短信验证码
     * @return string
     */
    public function quickLogin(Request $request)
    {
        $data = $request->only(['phone', 'sms_code']);
        //规则验证
        $validate = Validator::make($data, UserRequest::rules('quick_login'));
        if ($validate->fails()) {
            return response()->json(['status' => '0', 'msg' => $validate->messages()->first()]);
        }

        //逻辑验证
        $check_array = ['sms_code'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        //判断用户是否存在
        $user = User::where('phone', $data['phone'])->first();
        try {
            if ($user) {
                $data=$user->createToken('ibangoo')->accessToken;
                $data = array('access_token'=>$data);

                $user = $this->authentication($user,$data);
                return response()->json(['status' => '1', 'msg' => '登录成功', 'data' => $user]);
            } else {
                $user = new User();
                $user->phone = $data['phone'];
                $user->nickname = '用户_' . $this->getRandomString(6);
                $user->header = Config::get('constants.DEFAULT_HEADER');
                $user->save();

                $data=$user->createToken('ibangoo')->accessToken;

                $user = $this->authentication($user,array('access_token'=>$data));

                return response()->json(['status' => '1', 'msg' => '您已注册成功', 'data' => $user]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => '0', 'msg' => '登录失败']);
        }
    }

    /**
     * 第三方登录
     * @access public
     * @param int $type 1=微信,2=QQ,3=微博
     * @param int $open_id (微信,QQ,微博)
     * @return string
     */
    public function tripartiteLogin(Request $request)
    {
        $type = $request->input('type');

        $open_id = $request->input('open_id');

        $res = UserTripartite::getUserOpenId($type, $open_id);
        if ($res) {
            $user = User::find($res['user_id']);

            $data=$user->createToken('ibangoo')->accessToken;

            $user = $this->authentication($user,array('access_token'=>$data));

            return response()->json(['status' => '1', 'msg' => '登录成功', 'data' => $user]);
        } else {
            return response()->json(['status' => '0', 'msg' => '绑定手机号']);
        }
    }

    /**
     * 绑定手机号码
     * @access public
     * @param int $phone
     * @param int $sms_code
     * @param int $type 1=微信,2=QQ,3=微博
     * @param int $open_id (微信,QQ,微博)
     * @param string $header 头像
     * @param int $sex 性别
     * @return string
     */
    public function bindPhone(Request $request)
    {
        $data = $request->all();

        //规则验证
        $validate = Validator::make($data, UserRequest::rules('quick_login'));
        if ($validate->fails()) {
            return response()->json(['status' => '0', 'msg' => $validate->messages()->first()]);
        }

        //逻辑验证
        $check_array = ['sms_code', 'phone_login'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        try {
            //用户表
            $user = new User();
            $user->phone = $data['phone'];
            $user->nickname = '用户_' . $this->getRandomString(6);
            $user->header = $data['header'];
            $user->sex = $data['sex'];
            $user->save();

            $data=$user->createToken('ibangoo')->accessToken;

            $user = $this->authentication($user,array('access_token'=>$data));

            //第三方表
            $user_tripartite = new UserTripartite();
            $user_tripartite->user_id = Auth::id();
            $user_tripartite->type = $data['type'];
            $user_tripartite->open_id = $data['open_id'];
            $user_tripartite->save();

            return response()->json(['status' => '1', 'msg' => '绑定成功', 'data' => $user]);
        } catch (\Exception $e) {
            return response()->json(['status' => '0', 'msg' => '绑定失败']);
        }
    }

    /**
     * 发送短信验证码
     * @access public
     * @param int $phone 手机号码
     * @param int $type 1=注册,2=忘记密码,3=快捷登录,4=绑定手机号码,5=修改手机号码,6=修改密码
     * @return string
     */
    public function sendSms(Request $request)
    {
        $data = $request->only(['phone', 'type']);

        //规则验证
        $validate = Validator::make($data, UserRequest::rules('send_sms'));
        if ($validate->fails()) {
            return response()->json(['status' => '0', 'msg' => $validate->messages()->first()]);
        }

        //逻辑验证
        switch ($data['type']) {
            case 1:
                $check_array = ['phone_already_exists', 'sms_frequently'];
                break;
            case 2:
                $check_array = ['phone_not_exists', 'sms_frequently'];
                break;
            case 3:
                $check_array = ['sms_frequently'];
                break;
            case 4:
                $check_array = ['phone_login', 'sms_frequently'];
                break;
            case 5:
                $check_array = ['bind_phone', 'sms_frequently'];
                break;
            case 6:
                $check_array = ['sms_frequently'];
                break;
        }
        $phone_logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($phone_logic_verification) {
            return $phone_logic_verification;
        }

        //$code = mt_rand(100000,999999);
        //实际开发中  更换阿里云 短信
        //$rs = Sms::sendSms($code,$phone);

        //测试专用
        $rs = 1;
        $code = 111111;
        if ($rs == 1) {
            $user = new Sms();
            $user->phone = $data['phone'];
            $user->sms_code = $code;
            $user->save();
            return response()->json(['status' => '1', 'msg' => '发送成功']);
        } else {
            return response()->json(['status' => '0', 'msg' => '发送失败，稍后重试']);
        }
    }

    /**
     * 绑定第三方账号
     * @access public
     * @param int $resource_type 1=绑定,2=解除绑定
     * @param int $type 1=微信,2=QQ,3=微博
     * @param int $open_id 第三方标识ID
     * @return string
     */
    public function bindTripartite(Request $request)
    {
        $user = Auth::user()->toArray();

        $type = $request->input('type');
        $open_id = $request->input('open_id');
        $resource_type = $request->input('resource_type');
        if (empty($type) || empty($open_id) || empty($resource_type)) {
            return response()->json(['status' => '0', 'msg' => '参数错误']);
        }
        $account_number = UserTripartite::getUserOpenId($type, $open_id);
        if ($resource_type == 1) {
            if ($account_number) {
                return response()->json(['status' => '0', 'msg' => '您已绑定其他账号']);
            }
            $user_tripartite = new UserTripartite();
            $user_tripartite->user_id = $user['id'];
            $user_tripartite->type = $type;
            $user_tripartite->open_id = $open_id;
            $user_tripartite->save();
            return response()->json(['status' => '1', 'msg' => '为了您的账号安全，30天内不可解除账号关联']);
        } else if ($resource_type == 2) {
            if (strtotime($account_number['created_at']) > time() - 30 * 24 * 3600) {
                return response()->json(['status' => '0', 'msg' => '解除失败，为了您的账户安全，30天内不可解除账号关联']);
            }
            $user_tripartite = UserTripartite::where('user_id', $user['id'])->first();
            $user_tripartite->delete();
            return response()->json(['status' => '1', 'msg' => '解绑成功']);
        }
    }

    /**
     * 修改手机号码
     * @access public
     * @param string $token
     * @param int $phone 手机号码
     * @param int $sms_code 短信验证码
     * @return string
     */
    public function modifyPhone(Request $request)
    {
        $user = Auth::user()->toArray();

        $data = $request->only(['phone', 'sms_code']);

        //规则验证
        $validate = Validator::make($data, UserRequest::rules('quick_login'));
        if ($validate->fails()) {
            return response()->json(['status' => '0', 'msg' => $validate->messages()->first()]);
        }

        //逻辑验证
        $check_array = ['sms_code'];
        $logic_verification = UserRequest::logicVerification($check_array, $data);
        if ($logic_verification) {
            return $logic_verification;
        }

        $user = User::find($user['id']);
        $user->phone = $data['phone'];
        $user->save();

        return response()->json(['status' => '1', 'msg' => '换绑手机号码成功']);
    }

    /**
     * 修改密码
     * @access public
     * @param int $sms_code 短信验证码
     * @param int $type 1= 输入验证码 2 设置新密码
     * @param int $password 新密码
     * @param int $password_confirmation 确认密码
     * @return string
     */
    public function modifyPassword(Request $request)
    {

        $user = Auth::user()->toArray();

        $type = $request->input('type');
        if ($type == 1) {
            $user = User::find($user['id']);
            $data = $request->only(['sms_code']);

            //规则验证
            $validate = Validator::make($data, UserRequest::rules('modify_password_one'));
            if ($validate->fails()) {
                return response()->json(['status' => '0', 'msg' => $validate->messages()->first()]);
            }

            //逻辑验证
            $check_array = ['sms_code'];
            $data = array_add($data, 'phone', $user->phone);
            $logic_verification = UserRequest::logicVerification($check_array, $data);
            if ($logic_verification) {
                return $logic_verification;
            }

            return response()->json(['status' => '1', 'msg' => '短信验证成功']);
        } else if ($type == 2) {
            $data = $request->only(['password', 'password_confirmation']);
            //规则验证
            $validate = Validator::make($data, UserRequest::rules('modify_password_two'));
            if ($validate->fails()) {
                return response()->json(['status' => '0', 'msg' => $validate->messages()->first()]);
            }
            $user = User::find($user['id']);
            $user->password = bcrypt($data['password']);
            $user->save();
            return response()->json(['status' => '1', 'msg' => '密码更换成功']);
        }
    }


    /**
     * 退出登录
     * @return array
     */
    public function logout(Request $request)
    {
        if (Auth::guard('api')->check()){
            Auth::guard('api')->user()->token()->revoke();
        }
        return response()->json(['status' => '1', 'msg' => '退出成功']);
    }


     /**
     * 获取用户信息
     * @return array
     */
    public function getUserInfo()
    {
        $data = Auth::user()->toArray();
        
        return response()->json(['status' => '1', 'msg' => '成功', 'data'=>$data]);
    }
}
