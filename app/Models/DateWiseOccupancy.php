<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DateWiseOccupancy extends Model
{
    public $timestamps = true;

    protected $table = 'date_wise_occupancies';
    
    protected $fillable = [
         "room_id",
        "date",
        "occupancy",
        ];

}