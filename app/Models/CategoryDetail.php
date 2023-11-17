<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CategoryDetail extends Model
{
    use SoftDeletes;


    function parentId()
    {
        return $this->hasOne('App\Models\CategoryDetail', 'parent_id', 'id');
    }
    
     function catParentId()
    {
        return $this->belongsTo('App\Models\CategoryDetail', 'parent_id');
    }
    
    public function setParentIdAttribute($value){
        $this->attributes['parent_id'] = is_null($value) ? 0 : $value;
    }
}
