<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use  App\Models\Post;
use  App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    protected $comment;
    protected $user;
    public function __construct(Comment $comment)
    {
        $this->middleware('auth');
        $this->comment = $comment;
        $this->user = Auth::user();
    }
    /**
     * get all comments related to post
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = $this->comment->with([
            'post' => function ($q) {
                $q->select('id', 'user_id');
            },
            'user' => function ($q) {
                $q->select('id', 'uuid', 'name', 'first_name', 'middle_name', 'last_name');
            },
            'user.user_meta' => function ($q) {
                $q->select('user_id', 'display_picture');
            }
        ])->where('post_id', $request->post_id);
        return response()->json(['comments' => $query->latest()->paginate(5), 'message' => 'All Comments Fetched'], 200);
    }
    /**
     * store new resource
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'comment' => 'required'
        ]);
        return $this->comment->create([
            'user_id' =>  Auth::user()->id,
            'text'    =>  $request->comment,
            'post_id' =>  $request->post_id,
        ]);
    }
    /**
     * delete Resource.
     *
     * @return Response
     */

    public function destroy(Request $request)
    {
        $comment = Comment::find($request->id);
        if (!isset($comment)) {
            return response()->json(['message' => 'Error', 'errors' => 'Comment not found.'], 404);
        }
        $comment->delete();
        return response()->json(['message' => 'Comment deleted.'], 200);
    }
}
