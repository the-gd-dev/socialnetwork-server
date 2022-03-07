<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\PostPhoto;
use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use App\Models\UserMeta;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
class UserController extends Controller
{
     /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }
    
    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function profile()
    {   $user  = auth()->user();
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
        $loggedInUserId = auth()->user()->id;
        $isFriend = Friend::where('is_friends', '1')
                          ->where(function($q) use($loggedInUserId, $user){
                            $q->where('user_id', $loggedInUserId)
                            ->orWhere('friend_id',  $user->id);
                          })->orWhere(function($q) use($loggedInUserId, $user){
                            $q->where('friend_id', $loggedInUserId)
                            ->orWhere('user_id',  $user->id);
                        })->exists();
        $isFriendRequestSent =  Friend::where('is_friends', '0')->where('user_id', $loggedInUserId)->where('friend_id', $user->id)->exists();
        try {
            if(!$user) {
                return response()->json(['message' => 'user not found!', 'error' => 'User not found.'], 404);
            }
            return response()->json(['user' => $user, 'is_friend' => $isFriend, 'is_request_sent' => $isFriendRequestSent], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'user not found!', 'error' => $e->getMessage()], 404);
        }

    }
    /**
     * Update the authenticated User.
     *
     * @return Response
     */
    public function update(Request $request)
    {   
        $userId  = Auth::user()->id;
        $updateData = [];
        $url = "";
        // If user choose existing image
        if($request->Has('display_picture')){
            $url = $request->display_picture;
            $updateData['display_picture'] = $url;
        }
        if($request->Has('cover')){
            $url = $request->cover;
            $updateData['cover'] = $url;
        }
        // If user upload new image
        if($request->hasFile('image')){
            $file  = $request->file('image');
            $photo = [];
            $fileName = Str::random(8).'-'.time().'.'.$file->extension();
            $originalName = $file->getClientOriginalName();
            list($w, $h, $mime) = getimagesize($file); //returns a list of attr
            $photo = [
                'user_id' => $userId,
                'name' =>   $fileName,
                'original_name' => $originalName,
                'dimensions' => $w.'x'.$h,
                'mime' => $mime,
                'size' => filesize($file)
            ];
            $file->move(base_path()."\storage\app\public\profile-picture\\".$userId , $fileName);
            $photo['url'] = env('APP_URL').'/storage/profile-picture/'.$userId .'/'.$fileName;
            $photos = PostPhoto::create($photo);
            $updateData[$request->type] = $photo['url'];
        }
        UserMeta::where('user_id', $userId)->update($updateData);
        return response()->json($updateData);
    }
}