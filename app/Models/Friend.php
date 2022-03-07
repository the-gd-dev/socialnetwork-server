<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Friend extends Model
{
    protected $table = 'friends';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'uuid',
        'friend_id',
        'relation_id',
        'relation_confirmation',
        'is_friends',
        'is_followed'
    ];
    public function friend(){
        return $this->hasOne(User::class, 'id', 'friend_id');
    }
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}