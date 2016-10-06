<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function fromFriends(){
        return $this->belongsToMany(User::class,'friends','from_user_id','to_user_id');
    }
    public function toFriends(){
        return $this->belongsToMany(User::class,'friends','to_user_id','from_user_id');
    }
}
