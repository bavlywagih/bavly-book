<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function images()
    {
        return $this->hasMany(CommentImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
