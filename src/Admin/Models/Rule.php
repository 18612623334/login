<?php
namespace App\Models\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Rule extends Base
{

    protected $table = 'admin_rule';

    public static function getList($where)
    {
        return self::where($where)->paginate(10);
    }

    public static function getFirst($where)
    {
        return self::where($where)->first();
    }


    public static function editorData($request)
    {
        $message = [
            'name.required' => '请添加路由名称',
            'url.required' => '请添加路由地址',
            'status.required' => '请添加状态',
            'naviagtion_id.required' => '请添加所属导航',
        ];

        $validator = Validator::make($request,[
            'name' => 'required|max:10',
            'url' => 'required',
            'status' => 'required',
            'naviagtion_id' => 'required',
        ],$message);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return ['status'=>0,'msg'=>$errors];
        }

        $data['name'] = htmlspecialchars($request['name']);
        $data['url'] = htmlspecialchars($request['url']);
        $data['naviagtion_id'] = htmlspecialchars($request['naviagtion_id']);
        $data['status'] = htmlspecialchars($request['status']);
        $data['parameter'] = htmlspecialchars($request['parameter']);

        if ($request['id']) {

            $where['id'] = $request['id'];
            $data['updated_at'] = date('Y-m-d H:i:s',time());
            $res = DB::table('admin_rule')->where($where)->update($data);

        } else {

            $data['created_at'] = date('Y-m-d H:i:s',time());
            $res = self::insertGetId($data);
        }

        if ($res) {

            return ['status' => '1' , 'msg' => '成功' , 'data' => $data['naviagtion_id']];

        } else {

            return ['status' => '0' , 'msg' => '失败'];

        }
    }


    public static function getRuleList()
    {
        $obj = self::leftjoin('admin_navigation','admin_navigation.id' , '=' , 'admin_rule.naviagtion_id')
            ->get(['admin_rule.id','admin_rule.name','admin_rule.url','admin_rule.naviagtion_id','admin_navigation.navigation_name']);

        return $obj = self::objectToArray($obj);
    }

    public static function getUserRuleList($group_id)
    {
        $obj = DB::table('admin_group_has_rule')
            ->where('group_gid','=',$group_id)
            ->select(['rule_rid'])
            ->get();

        $array_obj =  self::objectToArray($obj);

        $tmp = [];

        foreach ($array_obj as $value) {
            $tmp[] = $value['rule_rid'];
        }

        return $tmp;
    }


    public static function addRule($request)
    {
        $message = [
            'group_rule.required' => '请选择权限',
            'group_name.required' => '请添加用户组名称',
            'group_name.max' => '用户组名称情保持在10字以内',
            'group_id.required' => '请添加用户名称',
        ];

        $validator = Validator::make($request,[
            'group_rule' => 'required',
            'group_name' => 'required|max:10',
            'group_id' => 'required',
        ],$message);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return ['status'=>0,'msg'=>$errors];
        }

        $where['gid'] = $request['group_id'];
        $data['group_name'] = htmlspecialchars($request['group_name']);
        $data['created_at'] = date('Y-m-d H:i:s',time());

        $res = DB::table('admin_group')->where($where)->update($data);

        if ($res) {

            $array_rule = explode('$$$' , trim(htmlspecialchars($request['group_rule']),'$$$'));

            if ($array_rule) {

                //删除用户组的权限
                $delete_rule = DB::table('admin_group_has_rule')->where(['group_gid'=>$request['group_id']])->delete();

                $rule_data = [];

                foreach ($array_rule as $key => $value) {

                    $rule_data[] = ['rule_rid' => $value , 'group_gid' => $request['group_id'],'created_at' => date('Y-m-d H:i:s',time())];

                }

                $info = DB::table('admin_group_has_rule')->insert($rule_data);

                if ($info) {

                    return ['status' => '1' , 'msg' => '成功'];

                } else {

                    return ['status' => '0' , 'msg' => '失败'];

                }

            } else {

                return ['status' => '0' , 'msg' => '请选择权限信息'];

            }

        } else {

            return ['status' => '0' , 'msg' => '修改失败，请稍后重试'];

        }

    }

    public static function ruleAll()
    {
        $obj = self::get(['url']);

        $array_obj = self::objectToArray($obj);

        $tmp = [];
        foreach ($array_obj as $value) {

            $tmp[] = $value['url'];

        }

        return $tmp;

    }

    public static function userRuleData($rule_array)
    {
        $obj = self::whereIn('id',$rule_array)->get(['url']);

        $obj = self::objectToArray($obj);

        $tmp = [];

        foreach ($obj as $value) {

            $tmp[] = $value['url'];

        }
        return $tmp;
    }

    public static function AdminRuleRoute()
    {
        $obj = self::leftjoin('admin_navigation' , 'admin_navigation.id' , '=' , 'admin_rule.naviagtion_id')
            ->where(['admin_rule.status'=>'1'])
            ->orderBy('navigation_sort')
            ->get(['admin_rule.id','admin_rule.name','admin_rule.url','admin_rule.parameter','admin_rule.naviagtion_id','admin_navigation.navigation_name']);

        return self::objectToArray($obj);
    }

}
