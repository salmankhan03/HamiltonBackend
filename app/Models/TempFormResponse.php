<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;

class TempFormResponse extends Model
{
    
    protected $fillable = ['form_type_id', 'form_response', 'created_by' , 'file_name','room_id' , 'is_follow_up_incomplete'];

    public $timestamps = true;
    
    public function getCreatedAtAttribute($date)
    {
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
    }
    
    public function getUpdatedAtAttribute($date)
    {
        return Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d H:i:s');
    }
    
}

