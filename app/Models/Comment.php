<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
    'user_id',
    'post_id',
    'parent_id',
    'body',
];



    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user', 'loves', 'replies');
    }

    public function images()
    {
        return $this->hasMany(CommentImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function loves()
    {
        return $this->hasMany(CommentLove::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }


}
