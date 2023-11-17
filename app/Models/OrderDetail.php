<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class OrderDetail extends Model
{
    use SoftDeletes;



    function roomData()
    {
        return $this->hasOne('App\Models\RoomDetail', 'room_id', 'room_id');
    }

    function itemData()
    {
        return $this->hasOne('App\Models\ItemDetail', 'id', 'item_id');
    }
}
