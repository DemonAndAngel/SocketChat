<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function fromUser(){
        return $this->belongsTo(User::class,'from_user_id','id');
    }
    public function toUser(){
        return $this->belongsTo(User::class,'to_user_id','id');
    }
}
