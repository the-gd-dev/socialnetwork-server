<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use  App\Models\Post;
use App\Models\PostPhoto;
use App\Models\PostReaction;
use App\Models\PostVideo;
use  App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Owenoj\LaravelGetId3\GetId3;

use function PHPUnit\Framework\fileExists;
// public $storageBasePath;
//   public $storageBaseUri;
//   public function __construct()
//   {
//     $this->storageBasePath = base_path() . "\storage\app\public\\";
//     $this->storageBaseUri = env('APP_URL') . '/storage/post-videos/';
//   }
class PostsController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    
    protected $storageBasePath;
    protected $storageBaseUri;
    protected $post;
    protected $loggedInUser;
    public function __construct(Post $post)
    {
        $this->middleware('auth');
        $this->post = $post;
        $this->user = User::with('user_meta')->find(Auth::user()->id);
        $this->storageBasePath = base_path() . "\storage\app\public\\";
        $this->storageBaseUri = env('APP_STORAGE_URI');
    }
    /**
     * Create Authenticated User Post.
     *
     * @return Response
     */
    public function posts(Request $request)

    {
        $query = $this->post->query();
        $userId  = $this->user->id;
        $currentLoggedInUserId = $this->user->id;
        $query = $query->with([

            'reactions' => function ($q) use ($currentLoggedInUserId) {
                $q->where('user_id', $currentLoggedInUserId)->get();
            },
            'reactions.reaction',
            'photos',
            'video',
            'comments' => function ($q) {
                $q->latest()->first();
            },
            'comments.user' => function ($q) {
                $q->select('id', 'uuid', 'name', 'first_name', 'middle_name', 'last_name');
            },
            'comments.user.user_meta' => function ($q) {
                $q->select('user_id', 'display_picture');
            },
            'user' => function ($q) {
                $q->select('id', 'uuid', 'name', 'first_name', 'middle_name', 'last_name');
            },
            'user.user_meta' => function ($q) {
                $q->select('user_id', 'display_picture');
            }
        ]);
        if (isset($request->id)) {
            $userId = User::where('uuid', $request->id)->first()->id;
            $query = $query->where('user_id', $userId);
        }
        return response()->json(['posts' => $query->latest()->paginate(5), 'message' => 'All Posts Fetched'], 200);
    }
    /**
     * Create Authenticated User Post.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        try {
            $post = new Post;
            $photos = [];
            $videos = [];
            $post->user_id = $this->user->id;
            $post->text = $request->input('text');
            $post->save();
            if ($request->hasFile('photos')) {

                foreach ($request->file('photos') as $file) {
                    $photo = [];
                    $fileName = Str::random(8) . '-' . time() . '.' . $file->extension();
                    $originalName = $file->getClientOriginalName();
                    list($w, $h, $mime) = getimagesize($file); //returns a list of attr
                    $photo = [
                        'user_id' => $this->user->id,
                        'post_id' => $post->id,
                        'name' =>   $fileName,
                        'original_name' => $originalName,
                        'dimensions' => $w . 'x' . $h,
                        'mime' => $mime,
                        'size' => filesize($file)
                    ];
                    $path = $file->move($this->storageBasePath . "\post-images\\" . $this->user->id, $fileName);
                    $photo['url'] = $this->storageBaseUri . '/post-images/' . $this->user->id . '/' . $fileName;
                    array_push($photos, PostPhoto::create($photo));
                }
            }
            if ($request->hasFile('videos')) {

                foreach ($request->file('videos') as $file) {
                    $video = [];
                    $track = new GetId3($file);
                    $videoInfo = $track->extractInfo();
                    $fileName = Str::random(8) . '-' . time() . '.' . $file->extension();
                    $originalName = $file->getClientOriginalName();
                    $video = [
                        'user_id' => $this->user->id,
                        'post_id' => $post->id,
                        'name' =>   $fileName,
                        'original_name' => $originalName,
                        'duration' => $videoInfo['playtime_seconds'],
                        'resolution' => $videoInfo['video']['resolution_x'] . 'x' . $videoInfo['video']['resolution_y'],
                        'mime' => $videoInfo['mime_type'],
                        'size' => $videoInfo['filesize']
                    ];
                    $path = $file->move($this->storageBasePath . "\post-videos\\" . $this->user->id, $fileName);
                    $video['url'] = $this->storageBaseUri . '/post-videos/' . $this->user->id . '/' . $fileName;
                    array_push($videos, PostVideo::create($video));
                }
            }
            //return successful response
            return response()->json(['post' => $post, 'photo' => $photos, 'videos' => $videos, 'message' => 'Post Created'], 201);
        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'Error', 'errors' => $e->getMessage()], 409);
        }
    }
    /**
     * Find Resource.
     *
     * @return Response
     */
    public function getPost($postId)
    {
        $post = $this->post->with([
            'reactions' => function ($q) {
                $q->where('user_id', $this->user->id);
            },
            'reactions.reaction',
            'photos',
            'video',
            'user' => function ($q) {
                $q->select('id', 'uuid', 'name', 'first_name', 'middle_name', 'last_name');
            },
            'user.user_meta' => function ($q) {
                $q->select('user_id', 'display_picture');
            }
        ])->find($postId);
        if (!isset($post)) {
            return response()->json(['message' => 'Error', 'errors' => 'Post not found.'], 404);
        }
        return response()->json(['post' => $post, 'message' => 'Post fetched.'], 200);
    }
    /**
     * delete Resource.
     *
     * @return Response
     */

    public function destroy(Request $request)
    {

        $post = $this->post->find($request->post_id);
        if (!isset($post)) {
            return response()->json(['message' => 'Error', 'errors' => 'Post not found.'], 404);
        }
        Comment::where('post_id', $post->id)->delete();
        $photos = PostPhoto::where('post_id', $post->id)->get();
        $video = PostVideo::where('post_id', $post->id)->first();
        $videoPath = $this->storageBasePath . "\post-videos\\" . $this->user->id . '\\' . $video->name;
        if (file_exists($videoPath)) {
            unlink($videoPath);
            $video->delete();
        }
        foreach ($photos as $key => $photo) {
            if ($photo->url !== $this->user->user_meta->display_picture) {
                $photoPath = $this->storageBasePath . "\post-images\\" . $this->user->id . '\\' . $photo->name;
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
                $photo->delete();
            }
        }
        $post->delete();
        return response()->json(['message' => 'Post deleted.'], 200);
    }
    /**
     * set resource privacy.
     *
     * @return Response
     */

    public function setPrivacy(Request $request)
    {
        $post = $this->post->find($request->post_id);
        if (!isset($post)) {
            return response()->json(['message' => 'Error', 'errors' => 'Post not found.'], 404);
        }
        $post->update([
            'privacy_id' => $request->privacy_id
        ]);
        return response()->json(['post' => $post, 'message' => 'Post privacy updated.'], 200);
    }

    /**
     * set resource reaction.
     *
     * @return Response
     */
    public function setReaction(Request $request)
    {
        $post = $this->post->find($request->post_id);
        if (!isset($post)) {
            return response()->json(['message' => 'Error', 'errors' => 'Post not found.'], 404);
        }
        PostReaction::with('reaction')->updateOrCreate(['user_id' => $this->user->id, 'post_id' => $post->id], [
            'reaction_id' => $request->reaction_id
        ]);
        $updated = $this->post->find($request->post_id);
        $postReaction = PostReaction::with('reaction')->where('user_id', $this->user->id)->where('post_id', $post->id)->first();
        return response()->json(['postReaction' => $postReaction, 'reactionCounter' => $updated->ReactionCounter, 'message' => 'Post reaction updated.'], 200);
    }
}
