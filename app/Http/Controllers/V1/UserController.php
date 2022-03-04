<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use Illuminate\Support\Arr;
class UserController extends Controller
{
     /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function profile()
    {   $user  = Auth::user();
        return response()->json(['user' => [
            "id" => $user->uuid,
            "first_name" =>$user->first_name ,
            "middle_name" => $user->middle_name ,
            "last_name" => $user->last_name,
            "name" => $user->name,
            "first_name" => $user->first_name ,
        ], 'message' => 'user fetched'], 200);
    }

    /**
     * Get one user.
     *
     * @return Response
     */
    public function singleUser($id)
    {
        $user = User::with('user_meta');
        $uuid = strlen($id) === 36 ? $id : null;
        if(strlen($id) === 36){
            $user = $user->where('uuid',$uuid)->first();
        }else{
            $user = $user->where('id',$id)->first();
        }
        
        try {
            if(!$user) {
                return response()->json(['message' => 'user not found!', 'error' => 'User not found.'], 404);
            }
            return response()->json(['user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'user not found!', 'error' => $e->getMessage()], 404);
        }

    }

}