<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'content', 'url_img', 'url_file', 'user_id'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function inFavorite(){
        return $this->belongsToMany(User::class, 'user_post', 'user_id', 'post_id');
    }
}
