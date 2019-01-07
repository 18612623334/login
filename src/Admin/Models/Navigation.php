<?php
namespace App\Models\Admin;

class Navigation extends Base
{

    protected $table = 'admin_navigation';

    public static function getList($request)
    {
        return self::where(function ($q) use ($request) {

            $request->name && $q -> where('navigation_name' , 'like' , '%'.htmlspecialchars($request->name).'%');

        })
            ->paginate(10);
    }


    public static function getFirstOne($where)
    {
        return self::where($where)->first();
    }

    public static function updatedData($where,$data)
    {
        if ($where['id']) {

            return self::where($where)->update($data);

        } else {

            $data['created_at'] = date('Y-m-d H:i:s');
            return self::insertGetId($data);

        }

    }
}