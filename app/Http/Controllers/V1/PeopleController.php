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
        $userId = User::where('uuid', $request->userUuid)->first()->id;
        try {
            $friends = [];
            $friendsRecords = Friend::where('is_friends', "1")->where('user_id', $userId)->orWhere('friend_id', $userId)->select('id', 'friend_id', 'user_id')->get();
            foreach ($friendsRecords as $record) {
                $pushValue = ($record->user_id != $userId) ? $record->user_id : $record->friend_id;                    
                array_push($friends, $pushValue);
            }

            $people = User::with([
                'user_meta' => function ($q) {
                    $q->select('user_id', 'display_picture');
                }
            ])->whereNotIn('id',$friends)->select('id','uuid','first_name','middle_name', 'name')->get()->random(15);
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