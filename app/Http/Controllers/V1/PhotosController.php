<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use  App\Models\PostPhoto;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
     /**
     * Instantiate a new PhotosController instance.
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
    public function photos(Request $request){
        $query = PostPhoto::query();
        $userId = User::where('uuid', $request->userId)->first()->id;
        $photos = $query->with([
                            'user' => function($q){ $q->select('id','uuid'); },
                            'user.user_meta'  => function($q){ $q->select('user_id','display_picture', 'cover'); },
                        ])
                        ->where('user_id', $userId)
                        ->latest()
                        ->get();
        return response()->json(['photos' => $photos, 'message' => 'Photos fetched successfull.'], 200);
    }
    /**
     * remove photos
     * @param Request
     * @return $friend
     */
    public function destroy(Request $request){
        $photo = PostPhoto::find($request->id);
        $userId = auth()->user()->id;
        if($photo->url !== UserMeta::find($userId)->display_picture){
            $path = base_path()."\storage\app\public\post-images\\".$userId.'\\'.$photo->name;
            if(file_exists($path)){
                unlink($path);
            }
        }
        $photo->delete();
        return response()->json(['message' => 'Photo removed.'],200);
    }
}