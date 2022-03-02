<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'text',
        'reaction_id',
        'privacy_id',
        'location'
    ];
    protected $table = 'posts';
    public function user(){
        return $this->belongsTo(User::class);
    }
}