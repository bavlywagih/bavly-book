<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile',
        'password',
        'birthday',
        'gender',
    ];

public function images()
{
    return $this->hasMany(Image::class);
}

public function posts()
{
    return $this->hasMany(Post::class);
}


public function profilePhotos()
{
    return $this->images()->where('type', 'profile')->orderByDesc('created_at');
}

// public function coverPhotos()
// {
//     return $this->images()->where('type', 'cover')->orderByDesc('created_at');
// }

// public function currentCoverPhoto()
// {
//     return $this->images()->where('type', 'cover')->where('is_current', true)->first();
// }

// public function currentCoverPhoto()
// {
//     return $this->hasOne(Image::class)
//                 ->where('type', 'cover')
//                 ->where('is_current', true)
//                 ->latest('id'); // optional لتحسين الدقة
// }
public function currentCoverPhoto()
{
    return $this->hasOne(Image::class)->where('type', 'cover')->where('is_current', true);
}

public function currentProfilePhoto()
{
    return $this->hasOne(Image::class)->where('type', 'profile')->where('is_current', true);
}
public function commentLoves()
{
    return $this->hasMany(CommentLove::class);
}
public function comments()
{
    return $this->hasMany(Comment::class);
}

// public function currentProfilePhoto()
// {
//     return $this->hasOne(Image::class)
//                 ->where('type', 'profile')
//                 ->where('is_current', true)
//                 ->latest('id'); // optional لتحسين الدقة
// }


// public function currentProfilePhoto()
// {
//     return $this->images()->where('type', 'profile')->where('is_current', true)->first();
// }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
