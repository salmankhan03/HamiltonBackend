<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\RoomDetail;
use App\User;

class APIToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //First it will check header data.
        if ($request->header('Authorization')) {
            //It will decode token and then parse to check valid request or not.
            $token = $request->header('Authorization');
            $token = explode(" ", $token);
            if (is_array($token) && count($token) == 2 && in_array("Bearer", $token)) {
                $token = base64_decode(base64_decode($token[1]));
                if ($token != "") {
                    $token_parts = json_decode($token, true);
                    if (is_array($token_parts) && count($token_parts) == 3) {
                       if($token_parts["role"] == "admin" || $token_parts["role"] == "kitchen"){
                            $user_detail = User::where("id", $token_parts["user_id"])->first();
                        }else{
                            $user_detail = RoomDetail::where("id", $token_parts["user_id"])->first();
                        }
                     
                        if (!$user_detail) {
                            return response()->json(['ResponseCode' => "11", 'ResponseText' => "Unauthorised"], 200);
                        } else {
                            session(['user_details' => $user_detail,'role' => $token_parts["role"]]);
                            return $next($request);
                        }
                    }
                }
            }
        }
        return response()->json(['ResponseCode' => "11", 'ResponseText' => "Unauthorised"], 200);
    }
}
