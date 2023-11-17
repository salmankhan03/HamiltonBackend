<?php

namespace App\Models;
use App\ItemOption;
use App\ItemPreference;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ItemDetail extends Model
{
    use SoftDeletes;



    function categoryData()
    {
        return $this->hasOne('App\Models\CategoryDetail', 'id', 'cat_id');
    }


    public function options(){
        return $this->belongsTo(ItemOption::class);
    }
    
    
    public function preference(){
        return $this->belongsTo(ItemPreference::class);
    }
}
