<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class RoomDetail extends Model
{
    use SoftDeletes;

    // public function setPasswordAttribute($value){
    //     dd(dcrypt($value));
    //     $this->attributes['password'] = md5($value);
    //     dd($this->attributes);
    // }


}
