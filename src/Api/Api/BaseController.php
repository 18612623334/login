<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class BaseController extends Controller
{
    use  AuthenticatesUsers;
    public function __construct(){
        $this->middleware('api');
    }


    //调用认证接口获取授权码
    protected function authenticateClient(Request $request)
    {
        $credentials = $this->credentials($request);

        $data = $request->all();

        $request->request->add([
            'grant_type' => Config::get('constants.PASSPORT_GRANT_TYPE_PASSWORT'),
            'client_id' => Config::get('constants.PASSPORT_CLIENT_ID'),
            'client_secret' => Config::get('constants.PASSPORT_SECRET'),
            'username' => $credentials['phone'],
            'password' => $credentials['password'],
            'scope' => '*'
        ]);
        
        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        $response = \Route::dispatch($proxy);

        return $response;
    }


    protected function authenticated(Request $request)
    {
        return $this->authenticateClient($request);
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return $this->authenticated($request);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $msg = $request['errors'];
        $code = $request['code'];
        return response()->json(['status' => $code, 'msg' => $msg]);
    }

    protected function getRandomString($len, $chars = null)
    {
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        mt_srand(10000000 * (double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }


    /**
     * 认证用户 行使登录
     * @return array
     */
    protected function authentication($user,$data)
    {
        Auth::login($user);
        $user = Auth::user();
        $user = $user->only(['phone', 'nickname', 'header']);
        return array_merge($user,$data);
    }


    /**
     * Token 验证
     * @return array
     */
    protected function checkToken($token)
    {
        $res = User::getUid($token);
        $info = $res ? $res['id'] : '';
        return $info;
    }
}
