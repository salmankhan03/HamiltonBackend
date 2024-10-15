<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TempFormType extends Model
{
    public $timestamps = true;

    protected $fillable = ['name','form_fields','is_published' , 'allow_print' , 'allow_mail'];


}