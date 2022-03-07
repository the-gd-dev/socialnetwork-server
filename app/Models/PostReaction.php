<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PostReaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'post_id',
        'reaction_id'
    ];
    protected $table = 'post_reactions';
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function reaction(){
        return $this->hasOne(Reaction::class, 'id', 'reaction_id' );
    }
    public function posts(){
        return $this->belongsToMany(Post::class, 'id', 'post_id' );
    }
}