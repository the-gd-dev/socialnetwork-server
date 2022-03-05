<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    public function comments(){
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function reaction(){
        return $this->hasOne(Reaction::class, 'id', 'reaction_id' );
    }
    public function photos(){
        return $this->hasMany(PostPhoto::class, 'post_id', 'id');
    }
}