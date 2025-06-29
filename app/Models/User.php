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

public function profilePhotos()
{
    return $this->images()->where('type', 'profile')->orderByDesc('created_at');
}

public function coverPhotos()
{
    return $this->images()->where('type', 'cover')->orderByDesc('created_at');
}

public function currentProfilePhoto()
{
    return $this->profilePhotos()->first();
}

public function currentCoverPhoto()
{
    return $this->coverPhotos()->first();
}


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
