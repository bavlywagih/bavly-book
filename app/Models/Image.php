<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

protected $fillable = ['user_id', 'path', 'type', 'is_current'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
{
    return $this->belongsTo(Post::class);
}

}
