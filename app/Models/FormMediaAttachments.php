<?php

namespace App\Models;
use Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FormMediaAttachments extends Model
{   
    
    use SoftDeletes;

    protected $fillable = ['name', 'type','file_extension','form_response_id','size_in_kb', 'thumbnail'];

    public $timestamps = true;
    
    protected $appends = ['path' , 'thumbImage'];
    
    protected $hidden = ['created_at','deleted_at' , 'updated_at'];
    
    public function getPathAttribute(){
        return Storage::url('public/FormResponses/media/'.$this->name);
    }
    
    public function getThumbImageAttribute(){
        return $this->thumbnail ? Storage::url('public/FormResponses/media/thumbnail/'.$this->thumbnail) : null;
    }
    
}