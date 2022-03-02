<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use  App\Models\Friend;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class FriendsController extends Controller
{
     /**
     * Instantiate a new FriendsController instance.
     *
     * @return void
     */
    protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->middleware('auth');
    }
    /**
     * get friend requests list
     * @param Request
     * @return $friend
     */
    public function friendRequests(Request $request){
        $query = Friend::with([
            'user' => function($q){$q->select('id','uuid', 'name','first_name', 'middle_name', 'last_name'); }, 
            'user.user_meta' => function ($q) { $q->select('user_id', 'display_picture'); }
        ]);
        if($request->type == 'sent'){
            $requests =  $query->where('user_id', auth()->user()->id)
                               ->where('is_friends', '0')
                               ->latest()
                               ->get();
        }else{
            $requests =  $query->where('friend_id', auth()->user()->id)
                               ->where('is_friends', '0')
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
        $friendId = User::where('uuid',  $request->id)->first()->id;
        $remove = Friend::where('friend_id', $friendId)->where('user_id', auth()->user()->id)->delete();
        return response()->json(['message' => 'Friend request removed.'],200);
    }
    /**
     * confirm friend from friend request list
     * @param Request
     * @return $friend
     */
    public function confirmFriendRequest(Request $request){
        
    }
}