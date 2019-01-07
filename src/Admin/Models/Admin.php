<?php

namespace App\Models\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Admin extends Base
{
    protected $table = 'admin';

    public static function getAdminFromAccount($account){

        $info = self::where('account',$account)
            ->first();

        return self::objectToArray($info);

    }

    public static function getAdminData($where)
    {
        $admin_data = self::leftjoin('admin_user_has_group' , 'admin_user_has_group.admin_id' , '=' , 'admin.id')
            ->where(['admin.id'=>$where])
            ->first(['admin.id','admin.account','admin.username','admin.status','admin_user_has_group.group_id']);

        return $admin_data;
    }


    public static function getList($request)
    {
        $obj = self::where(function ($q) use ($request) {
            $request -> name && $q -> where('username' , 'like' , '%'.htmlspecialchars($request->name).'%');
        })->paginate(10);

        foreach ($obj as $key => $value) {
            $array_obj = DB::table('admin_user_has_group')
                ->leftjoin('admin_group as group' , 'group.gid' , '=' , 'admin_user_has_group.group_id')
                ->where('admin_user_has_group.admin_id' , '=' , $value['id'])
                ->get();
            $array = self::objectToArray($array_obj);

            $array_group = [];
            foreach ($array as $k => $v) {

                $array_group[] = $v['group_name'];

            }
            $array_group = implode(',' , $array_group);
            $obj[$key]['group'] = $array_group;
        }
        return $obj;
    }

    public static function updateStatus($where,$data)
    {
        return DB::table('admin')->where($where)->update($data);
    }

    /**
     * 管理员信息编辑、添加
     * @param $request
     * @return array
     */
    public static function updateAdminData($request)
    {
        $message = [
            'admin_name.required' => '请添加管理员姓名',
            'account.required' => '请添加管理员账号',
            'status.required' => '请选择管理员状态'
        ];

        $validator = Validator::make($request,[
            'admin_name' => 'required',
            'account' => 'required',
            'status' => 'required'
        ],$message);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return ['status'=>0,'msg'=>$errors];
        }

        if ($request['password'] && $request['password_confirm']) {
            if ($request['password'] != $request['password_confirm'] || strlen($request['password']) < 6 || strlen($request['password']) > 16) {
                return ['status' => 0, 'msg' => '密码不合理'];
            }
            $data['password'] = bcrypt($request['password']);
        }

        if (!$request['id'] && empty($request['password'])) {
            return ['status' => 0, 'msg' => '请设置密码'];
        }

        if ($request['id'] == '1' && $request['status'] == '0') {
            return ['status' => '0', 'msg' => '请勿修改管理员信息'];
        }

        if ($request['id'] != '1') {

            if ($request['group_id'] == '')
            {
                return ['status' => '0' , 'msg' => '请选择管理员类型'];
            }

            $group_id['group_id'] = $request['group_id'];
        }

        $data['username'] = htmlspecialchars($request['admin_name']);
        $data['account'] = htmlspecialchars($request['account']);
        $data['status'] = $request['status'];

        if ($request['id']) {

            $where['id'] = $request['id'];
            $data['updated_at'] = date('Y-m-d H:i:s');

            $admin_status = self::where($where)->update($data);

            if (!$admin_status) {
                return ['status' => '0' , 'msg' => '请检查用户组信息'];
            } else {
                if ($where['id'] != '1') {
                    //修改管理员用户组信息
                    $group_id['created_at'] = date('Y-m-d H:i:s',time());
                    $group_where['admin_id'] = $request['id'];
                    $admin_group = DB::table('admin_user_has_group')->where($group_where)->update($group_id);
                    if ($admin_group) {
                        return ['status' => '1' , 'msg' => '成功'];
                    } else {
                        $group_id['admin_id'] = $request['id'];
                        //添加用户组信息
                        $admin_group_insert = DB::table('admin_user_has_group')->insertGetId($group_id);
                        if ($admin_group_insert) {
                            return ['status' => '1' , 'msg' => '成功'];
                        }
                    }
                }
                return ['status' => '1' , 'msg' => '信息修改成功'];
            }
        } else {
            $data['created_at'] = date('Y-m-d H:i:s',time());
            $admin_status = self::insertGetId($data);
            if ($admin_status) {
                //用户组
                $group_id['admin_id'] = $admin_status;
                $group_id['created_at'] = date('Y-m-d H:i:s');
                $group_add_data = DB::table('admin_user_has_group')->insertGetId($group_id);

                if ($group_add_data) {
                    return ['status' => '1' , 'msg' => '成功'];
                }
                return ['status' => '0' , 'msg' => '请检查参数是否正确'];
            } else {
                return ['status' => '0' , 'msg' => '请检查参数是否正确'];
            }
        }
    }

    public static function AdminUserGroupLis($admin_id)
    {

        $obj = DB::table('admin_user_has_group')
            ->leftjoin('admin_group_has_rule' , 'admin_user_has_group.group_id' , '=' , 'admin_group_has_rule.group_gid')
            ->leftjoin('admin_rule' , 'admin_rule.id' , '=' , 'admin_group_has_rule.rule_rid')
            ->where(['admin_rule.status'=>'1'])
            ->where(['admin_user_has_group.admin_id'=>$admin_id])
            ->get(['admin_rule.naviagtion_id','admin_rule.id']);

        $array_obj = self::objectToArray($obj);

        return $array_obj;

    }


}