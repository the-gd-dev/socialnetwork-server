<?php

namespace App\Http\Controllers\V1;
use App\Http\Controllers\Controller;
use  App\Models\PostPhoto;
use App\Models\User;
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
        $photos = $query->with([
                            'user' => function($q){ $q->select('id','uuid'); }, 
                        ])
                        ->where('user_id', User::where('uuid', $request->userId)->first()->id)
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
        $remove = PostPhoto::find($request->id)->delete();
        return response()->json(['message' => 'Photo removed.'],200);
    }
}