<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use  App\Models\User;
use  App\Models\Friend;
use Illuminate\Http\Request;

class FriendsController extends Controller
{
     /**
     * Instantiate a new FriendsController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * get friends list
     * @param Request
     * @return $friend
     */
    public function friends(Request $request){
        $query = Friend::query();
        $friends = $query->where('user_id', User::where('uuid', $request->userId)->first()->id)
                        ->where('is_friends', '1')
                        ->with([
                            'user' => function($q){$q->select('id','uuid', 'name','first_name', 'middle_name', 'last_name'); }, 
                            'user.user_meta' => function ($q) { $q->select('user_id', 'display_picture', 'bio_text'); }
                        ])
                        ->latest()
                        ->get();
        return response()->json(['friends' => $friends, 'message' => 'Friends fetched successfull.'], 200);
    }
    /**
     * get friend requests list
     * @param Request
     * @return $friend
     */
    public function friendRequests(Request $request){
        $query = Friend::query();
        if($request->type == 'sent'){
            $requests =  $query->where('user_id', auth()->user()->id)
                               ->with([
                                    'user' => function($q){$q->select('id','uuid', 'name','first_name', 'middle_name', 'last_name'); }, 
                                    'user.user_meta' => function ($q) { $q->select('user_id', 'display_picture'); }
                                ])
                               ->where('is_friends', '0')
                               ->latest()
                               ->get();
        }else{
            $requests =  $query->where('friend_id', auth()->user()->id)
                               ->where('is_friends', '0')
                               ->with([
                                    'request' => function($q){$q->select('id','uuid', 'name','first_name', 'middle_name', 'last_name'); }, 
                                    'request.user_meta' => function ($q) { $q->select('user_id', 'display_picture'); }
                                ])
                               ->latest()
                               ->get();
        }
        
        return response()->json(['requests' => $requests], 200);
    }
    /**
     * add a new friend from random list
     * @param Request
     * @return $friend
     */
    public function addFriend(Request $request){
        $personId = User::where('uuid', $request->id)->first()->id;
        $friend = Friend::updateOrCreate(
            ['user_id' => auth()->user()->id, 'friend_id' => $personId],
            [
                'user_id' => auth()->user()->id, 
                'friend_id' => $personId
            ]
        );
        return $friend;
    }
    /**
     * remove person from friend request list
     * @param Request
     * @return $friend
     */
    public function removeFriendRequest(Request $request){
        $remove = Friend::find($request->id)->delete();
        return response()->json(['message' => 'Friend request removed.'],200);
    }
    /**
     * confirm friend from friend request list
     * @param Request
     * @return $friend
     */
    public function confirmFriendRequest(Request $request){
        $friend = Friend::find($request->id)->update([
            'is_friends' =>  '1'
        ]);
        return response()->json(['friend' => $friend, 'message' => 'Friend request confirmed.'],200);
    }
}