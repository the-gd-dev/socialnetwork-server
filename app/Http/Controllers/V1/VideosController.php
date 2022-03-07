<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use  App\Models\PostVideo;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Http\Request;

class VideosController extends Controller
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
     * get vidoes list
     * @param Request
     * @return $friend
     */
    public function videos(Request $request){
        $query = PostVideo::query();
        $userId = User::where('uuid', $request->userId)->first()->id;
        $videos = $query->with([
                            'user' => function($q){ $q->select('id','uuid'); },
                        ])
                        ->where('user_id', $userId)
                        ->latest()
                        ->get();
        return response()->json(['videos' => $videos, 'message' => 'Videos fetched successfull.'], 200);
    }
    /**
     * remove videos
     * @param Request
     * @return $friend
     */
    public function destroy(Request $request){
        $video = PostVideo::find($request->id);
        $userId = auth()->user()->id;
        $path = base_path()."\storage\app\public\post-videos\\".$userId.'\\'.$video->name;
        if(file_exists($path)){
            unlink($path);
        }
        $video->delete();
        return response()->json(['message' => 'Video removed.'],200);
    }
}