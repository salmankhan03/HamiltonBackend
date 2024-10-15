<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

use App\Models\BackendUser;
use App\Models\RoleHasPermission;


class BackendRole extends Model{
    
    
     protected $fillable = [
         "name",
         "guard_name"
        ];
    
    public function users(){
        return $this->hasMany(BackendUser::class , "role_id" , "id");
    }
    
    public function permissions(){
        return $this->hasManyThrough(BackendPermission::class, RoleHasPermissions::class, 'role_id', 'id', 'id', 'permission_id');
    }
}