<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\BackendRole;
use App\Models\RoleHasPermission;



class BackendUser extends Authenticatable implements JWTSubject {
    
    use HasRoles;
    
    protected $table = 'backend_users';
    
      protected $fillable = [
         "name",
         "email",
         "password",
         "role_id"
        ];
    
       /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    
    public function role(){
        return $this->hasOne(BackendRole::class,"id" , "role_id");
    }
    
    public function permissions(){
        return $this->hasManyThrough(BackendPermission::class, RoleHasPermissions::class, 'role_id', 'id', 'role_id', 'permission_id');
    }
    
}