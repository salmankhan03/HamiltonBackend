<?php


namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\BackendRole;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleController extends Controller
{
    public function __construct(){
      
        ini_set('max_execution_time', 0);
      
    }
    
    public function upsert(Request $request){
        try{
            
            $id = $request->input('id');
            
            $name = $request->input('name');
            
            //first param criteria and second is data
            $role = BackendRole::updateOrCreate([
                   'id' => $id
                ],[
                   'name' => $name,
                   'guard_name' => "backend-api"
                ]
            );
            
            return response()->json([ 'message' =>  'Role Created Successfully'], 200);
        }
        
        catch (\Exception $e) {
            return $this->sendResultJSON("0", $e->getMessage());
        }
        
    }
    
    public function delete($id){
        
        // if you delete roles 
        // then
        // delete users
        // delete rows from role_has_permission
        
        try{
            if ($id){
                
                BackendRole::where("id", $id)->delete();
                return response()->json([ 'message' =>  'Role Deleted Successfully'], 200);
                
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
            $list = BackendRole::with('permissions')->select('id','name')->get();
            return response()->json([ 'list' =>  $list], 200);

        }
        catch(\Exception $e){
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
    
    public function getById($id){
        try{
            if ($id){
                
                $role = BackendRole::where("id", $id)->get();
                return response()->json([ 'role' =>  $role], 200);
                
            }else{
                return response()->json([ 'message' =>  'Please Specify Id'], 500);
            }
            
        }
        
        catch (\Exception $e){
            return response()->json([ 'message' => $e->getMessage()], 500);
        }
    }
    
    public function getUserTree(){
        try {
            
            $list = BackendRole::with('users')->get();
            return response()->json([ 'list' =>  $list], 200);
            
        }
        catch (\Exception $e){
            return $this->sendResultJSON("0", $e->getMessage());
        }
    }
    
    public function syncPermission(Request $request){
        
        try{
            
            $roleId = $request->input('roleId');
            
            $permissions = json_decode($request->input('permissions'),true); // should be permission array ['edit articles', 'delete articles']
            
            $role = SpatieRole::find($roleId);
        
            $role->syncPermissions($permissions);
            
            return response()->json([ 'message' =>  "Permissions Synced Successfully"], 200);
            
        }
        
        catch (\Expcetion $e){
            return response()->json([ 'message' => $e->getMessage()], 500);
        }
        

    }
}