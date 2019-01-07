<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account' => ['required', 'regex:/^[a-z\d]*$/i'],
            'password' => ['required', 'between:6,15', 'regex:/^([A-Z]|[a-z]|[0-9]|[_.]){6,15}$/'],
            'captcha' => ['required', 'captcha'],
        ];
    }

    public function message()
    {
        return [
            'account.required' => '请输入用户名',
            'account.regex' => '不允许输入特殊字符',
            'password.required' => '请输入密码',
            'password.between' => '请输入6~15位密码',
            'password.regex' => '只允许数字、字母、下划线',
            'captcha.required' => '请输入图片验证码',
        ];
    }

    public function attributes()
    {
        return [
            'account' => '账号',
            'captcha' => '图片验证码',
        ];
    }
}
