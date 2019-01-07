<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Admin;
use App\Models\Admin\Group;
use App\Models\Admin\Navigation;
use App\Models\Admin\Rule;
use Illuminate\Http\Request;

class AdminController extends BaseController
{

    /**
     * 管理员列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $list = Admin::getList($request);

        return view('Admin.Admin.index', [
            'list' => $list
        ]);
    }

    /**
     * 管理员编辑
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editorAdmin()
    {
        $admin_id = $this->getInput('admin_id');

        $admin_data = Admin::getAdminData($admin_id);

        //管理员用户组的划分
        $group_list = Group::getGroupList();

        return view('Admin.Admin.editor', [
            'admin_data' => $admin_data,
            'group_list' => $group_list
        ]);

    }

    /**
     * 超级管理员状态
     *
     * @return array
     */
    public function adminStatus()
    {
        $admin_id = $this->getInput('admin_id');
        $status = $this->getInput('status');

        if ($admin_id == '1') {

            return ['status' => '0', 'msg' => '请勿修改超级管理信息'];
        }

        $where['id'] = $admin_id;
        $data['status'] = $status;

        $res = Admin::updateStatus($where, $data);

        if ($res) {

            return ['status' => '1', 'msg' => '成功'];

        } else {

            return ['status' => '0', 'msg' => '请检查数据是否正确'];

        }
    }


    /**
     * 管理员操作
     *
     * @param Request $request
     * @return array
     */
    public function adminUpdateData(Request $request)
    {

        $update_data = Admin::updateAdminData($request->all());

        return $update_data;
    }


    /**
     * 用户组列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminGroup(Request $request)
    {

        $group_list = Group::getRequestList($request);

        return view('Admin.Admin.group', [
            'list' => $group_list
        ]);

    }

    /**
     * 用户组编辑页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function groupEditor()
    {
        $group_id = $this->getInput('group_id');

        $group_info = Group::getGroupEditor(['gid' => $group_id]);

        return view('Admin.Admin.groupEditor', [
            'group_info' => $group_info
        ]);

    }

    /**
     * 用户组名称保存
     *
     * @param Request $request
     * @return array
     */
    public function groupCreated(Request $request)
    {
        $group_data = Group::groupUpdate($request->all());

        return $group_data;
    }


    public function ruleRoute(Request $request)
    {
        $list = Navigation::getList($request);

        return view('Admin.Admin.rule', [
            'list' => $list
        ]);
    }

    public function navigationEditor()
    {
        $id = $this->getInput('id');

        $res = Navigation::getFirstOne(['id' => $id]);

        return view('Admin.Admin.navigationEditor', [
            'admin_data' => $res
        ]);
    }

    public function navigationUpdate()
    {
        $navigation_name = $this->getInput('navigation_name');
        $id = $this->getInput('id');
        $sort = $this->getInput('sort');

        $data['navigation_name'] = $navigation_name;
        $data['navigation_sort'] = $sort;
        $where['id'] = $id;

        $res = Navigation::updatedData($where, $data);

        if ($res) {

            return ['status' => '1', 'msg' => '成功'];

        } else {

            return ['status' => '0', 'msg' => '失败'];

        }
    }


    /**
     * 路由列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function routeList()
    {
        $id = $this->getInput('id');

        $res = Rule::getList(['naviagtion_id' => $id]);

        return view('Admin.Admin.ruleList', [
            'list' => $res,
            'id' => $id
        ]);
    }

    /**
     * 路由编辑页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function routeEditor()
    {
        $id = $this->getInput('id');
        $naviagtion_id = $this->getInput('naviagtion_id');

        $res = Rule::getFirst(['id' => $id]);

        return view('Admin.Admin.ruleEditor', [
            'info' => $res,
            'naviagtion_id' => $naviagtion_id
        ]);
    }

    public function editorRuleData(Request $request)
    {
        $info = Rule::editorData($request->all());

        return $info;
    }


    public function authorization()
    {
        $group_id = $this->getInput('group_id');

        //路由信息
        $rule_route = Rule::getRuleList();

        $array_route = [];
        foreach ($rule_route as $key => $value) {

            $array_route[$value['naviagtion_id']][] = $value;

        }

        //用户组信息
        $group_name = Group::getGroupEditor(['gid' => $group_id]);

        //用户权限
        $rule = Rule::getUserRuleList($group_id);

        return view('Admin.Admin.groupRule', [
            'array_route' => $array_route,
            'group_name' => $group_name,
            'rule' => $rule
        ]);
    }


    //定义路由错误页面
    public function ruleErrors()
    {
        return view('404');
    }

    public function groupRuleData(Request $request)
    {

        $info = Rule::addRule($request->all());

        return $info;
    }

}