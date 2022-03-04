<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PostPhoto extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'post_id',
        'name',
        'original_name',
        'url',
        'dimensions',
        'mime',
        'size'
    ];
    protected $table = 'photos';
    public function user(){
        return $this->belongsTo(User::class);
    }
}