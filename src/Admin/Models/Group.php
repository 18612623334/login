<?php

namespace App\Models\Admin;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class Group extends Base
{

    protected $table = 'admin_group';

    public static function getGroupList()
    {
        $obj = self::get();

        return self::objectToArray($obj);
    }


    public static function getRequestList($request)
    {
        return self::where(function ($q) use ($request) {
                $request -> group_name && $q -> where('group_name' , 'like' , '%'.htmlspecialchars($request->group_name).'%');
            })
            ->paginate(10);
    }


    public static function getGroupEditor($where)
    {
        return self::where($where)->first();
    }

    public static function groupUpdate($request)
    {

        $message = [
            'group_name.required' => '请添加用户组名称',
            'group_name.max' => '请保持在10字以内'
        ];

        $validator = Validator::make($request,[
            'group_name' => 'required|max:10'
        ],$message);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return ['status'=>0,'msg'=>$errors];
        }
        
        if ($request['group_id']) {
            $data['group_name'] = htmlspecialchars($request['group_name']);
            $data['updated_at'] = date('Y-m-d H:i:s',time());
            $res = DB::table('admin_group')->where(['gid'=>$request['group_id']])->update($data);
        } else {
            $data['group_name'] = htmlspecialchars($request['group_name']);
            $data['created_at'] = date('Y-m-d H:i:s',time());
            $res = self::insertGetId($data);

        }

        if ($res) {

            return ['status' => '1' , 'msg' => '成功'];

        } else {

            return ['status' => '0' , 'msg' => '请检查信息是否正确'];

        }

    }


    public static function userGroup($admin)
    {

        $obj = DB::table('admin_user_has_group')->where(['admin_id'=>$admin])->first();

        return self::objectToArray($obj);

    }


    public static function userRule($group_id)
    {
        $obj = DB::table('admin_group_has_rule')->where(['group_id'=>$group_id])->get();

        $array = self::objectToArray($obj);

        $tmp = [];
        foreach ($array as $value) {

            $tmp[] = $value['rule_rid'];

        }

        return $tmp;

    }

}