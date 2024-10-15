<?php


namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\BackendUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Models\Permission as SpatiePermission;

class BackendUserController extends Controller
{
    public function __construct(){
      
        ini_set('max_execution_time', 0);
      
    }
    
    public function upsert(Request $request){
        try{
            
            $id = $request->input('id');
            
            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');
            $roleId = $request->input('role_id');
            
            if (!$id){
                BackendUser::create([
                    'name' => $name,
                    'email' => $email,
                    'role_id' => $roleId,
                    'password' => Hash::make($password)
                ]);
            }
            
            else{
                
                // in update we are not implementing update password mechanism
                
                //first param criteria and second is data
                
                $existingUserWithSameEmail = BackendUser::where('email' ,$email)->where('id' , '!=' , $id)->get();
                
                if ($existingUserWithSameEmail->count()){
                    
                    return response()->json([ 'message' =>  'Email Already Exist !'], 500);        
                    
                }
                
                $existingUserWithSameName = BackendUser::where('name' ,$name)->where('id' , '!=' , $id)->get();
                
                if ($existingUserWithSameName->count()){
                    
                    return response()->json([ 'message' =>  'Name Already Exist !'], 500);        
                    
                }
                
                
                $role = BackendUser::where([
                       'id' => $id
                    ])->update([
                       'name' => $name,
                        'email' => $email,
                        'role_id' => $roleId
                    ]
                );    
            }
            
            
            return response()->json([ 'message' =>  'User Saved Successfully'], 200);
        }
        
        catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
        
    }
    
    public function delete($id){
        

        
        try{
            if ($id){
                
                BackendUser::where("id", $id)->delete();
                return response()->json([ 'message' =>  'User Deleted Successfully'], 200);
                
            }else{
                return response()->json([ 'message' =>  'Please Specify Id'], 500);
            }


        }
        catch (\Exception $e){
                return response()->json([ 'message' => $e->getMessage()], 500);
        }
    }
    
    public function list(){
        try{
            $list = BackendUser::select('id','name' , 'email', 'role_id')->with('role')->get(); // role name needs to be added
            return response()->json([ 'list' =>  $list], 200);

        }
        catch(\Exception $e){
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
    
    public function getById($id){
        try{
            if ($id){
                
                $user = BackendUser::where("id", $id)->get();
                return response()->json([ 'user' =>  $user], 200);
                
            }else{
                return response()->json([ 'message' =>  'Please Specify Id'], 500);
            }
            
        }
        
        catch (\Exception $e){
            return response()->json([ 'message' => $e->getMessage()], 500);
        }
    }
    
    public function logout(){
        try {
            
            $user = auth('backend-api')->user();
            
            if ($user){
                
                auth('backend-api')->logout(true);
            }
            
            return response()->json([ 'message' =>  'Logged Out Successfully'], 200);

        }
        catch (\Exception $e){
            return response()->json([ 'message' => $e->getMessage()], 500);
        }
    }
    
    public function permissionList(){
        try {
            
            $list = SpatiePermission::select('id','name')->get();
            
            return response()->json([ 'list' =>  $list], 200);

        }
        catch (\Exception $e){
            return response()->json([ 'message' => $e->getMessage()], 500);
        }
    }
}