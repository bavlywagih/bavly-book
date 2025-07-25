<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Image;
use App\Models\User;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run()
{
    User::factory(10)->create()->each(function ($user) {
        Post::factory(rand(2, 5))->create(['user_id' => $user->id])->each(function ($post) use ($user) {
            $imageCount = rand(1, 5);

            for ($i = 0; $i < $imageCount; $i++) {
                $number = rand(1, 10);
                Image::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'type' => 'post',
                    'path' => 'post_images/' . $number . '.jpg',
                    'is_current' => false,
                ]);
            }
        });

        Image::create([
            'user_id' => $user->id,
            'post_id' => null,
            'type' => 'cover',
            'path' => 'cover_photos/' . rand(1, 10) . '.jpg',
            'is_current' => true,
        ]);

        Image::create([
            'user_id' => $user->id,
            'post_id' => null,
            'type' => 'profile',
            'path' => 'profile_photos/' . rand(1, 10) . '.jpg',
            'is_current' => true,
        ]);
    });
}
}
