<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'post_id',
        'text',
        'reaction_id',
        'privacy_id',
        'location'
    ];
    protected $table = 'posts';
    protected $appends = [
        'ReactionCounter',
        'CommentsCount'
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }
    public function reactions()
    {
        return $this->hasMany(PostReaction::class, 'post_id', 'id');
    }
    public function getReactionCounterAttribute()
    {
        return  [
            'happy' => $this->reactions()->where('reaction_id', 1)->count(),
            'angry' => $this->reactions()->where('reaction_id', 2)->count(),
            'sad' => $this->reactions()->where('reaction_id', 3)->count(),
            'love' => $this->reactions()->where('reaction_id', 4)->count(),
            'annoyed' => $this->reactions()->where('reaction_id', 5)->count()
        ];
    }
    public function photos()
    {
        return $this->hasMany(PostPhoto::class, 'post_id', 'id');
    }
    public function video()
    {
        return $this->hasOne(PostVideo::class, 'post_id', 'id');
    }
}
