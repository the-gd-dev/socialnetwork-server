<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Friend extends Model
{
    protected $table = 'friends';
    protected $with = ['user'];
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
    public function user(){
        return $this->hasOne(User::class, 'id', 'friend_id');
    }
}