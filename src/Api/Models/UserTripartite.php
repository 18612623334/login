<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class UserTripartite extends Model
{

    public static function getUserOpenId($type, $open_id)
    {
        $res = self::where(['type' => $type, 'open_id' => $open_id])->select('user_id', 'created_at')->first();
        if ($res) {
            return $res->toArray();
        } else {
            return false;
        }
    }
}
