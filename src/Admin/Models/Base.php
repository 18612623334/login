<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Base extends Authenticatable
{

    protected static function objectToArray($object){

        $list =  json_decode(json_encode($object), true);

        $list = $list?$list:[];

        return $list;

    }


}