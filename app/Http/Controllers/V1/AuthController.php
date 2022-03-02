<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string|min:3|max:30',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|digits:10|unique:users',
            'password' => 'required|confirmed'
        ]);

        try {

            $user = new User;
            $user->uuid = Str::uuid();
            $user->name = $request->input('name');
            $user->first_name = $request->input('first_name');
            $user->middle_name = $request->input('middle_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->phone_number = $request->input('phone_number');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(['user' => [
                "id" => $user->uuid,
                "first_name" =>$user->first_name ,
                "middle_name" => $user->middle_name ,
                "last_name" => $user->last_name,
                "name" => $user->name,
                "first_name" => $user->first_name ,
            ], 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!','errors' => $e], 409);
        }

    }
    
     /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ],[
            'email.exists' => 'This email does not exists.'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


}