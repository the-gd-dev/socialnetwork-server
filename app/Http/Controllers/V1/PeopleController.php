<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use  App\Models\Friend;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
class PeopleController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }
     /**
     * return a list of random users.
     *
     * @return Response
     */
    public function randomPeople(Request $request){
        try {
            $people = User::with([
                'user_meta' => function ($q) {
                    $q->select('user_id', 'display_picture');
                }
            ])->select('id','uuid','first_name','middle_name', 'name')->get()->random(15);
            return response()->json(['people' => $people ,'message' => 'people fetched'], 200);
        } catch (\Exception $th) {
            return response()->json(['message' => 'Server Error','message' => $th->getMessage()], 500);
        }
       
    }
    
    /**
     * remove a person from random users.
     *
     * @return Response
     */
    public function removePerson(){

    }
}