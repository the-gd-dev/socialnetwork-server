<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PostVideo extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'post_id',
        'name',
        'original_name',
        'url',
        'duration',
        'resolution',
        'mime',
        'size'
    ];
    protected $table = 'videos';
    public function user(){
        return $this->belongsTo(User::class);
    }
}