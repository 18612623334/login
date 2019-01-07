<?php

namespace App\Http\Controllers\Admin;


class IndexController extends BaseController
{

    public function index()
    {

       
        $admin_id = session('id');

        if ($admin_id) {
            return view('Admin.Index.index');

        } else {

            return redirect()->route('login.index');

        }
    }

    public function welcome()
    {

        $admin_id = session('id');

        if ($admin_id) {
            return view('Admin.Index.welcome');

        } else {

            return redirect()->route('login.index');

        }

    }

    public function authError()
    {
        return view('Admin.Index.authError');
    }


    public function getEchart()
    {
        $arr = array('visit'=>[90, 850, 950, 1e3, 1100, 1050, 1e3, 1150, 1250, 1370, 1250, 1100],
                     'download'=>[85, 850, 800, 950, 1e3, 950, 950, 1150, 1100, 1240, 1e3, 950]);

        return response()->json(['status' => '1', 'msg' => '成功', 'data' => $arr]);
    }

}
