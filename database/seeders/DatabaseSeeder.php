<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Post;
use App\Models\Image;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
public function run(): void
{

    User::factory(50)->create()->each(function ($user) {


        $cover = Image::factory()->create([
            'user_id' => $user->id,
            'type' => 'cover',
            'is_current' => true,
        ]);


        Image::factory(2)->create([
            'user_id' => $user->id,
            'type' => 'cover',
        ]);


        $profile = Image::factory()->create([
            'user_id' => $user->id,
            'type' => 'profile',
            'is_current' => true,
        ]);


        Image::factory(2)->create([
            'user_id' => $user->id,
            'type' => 'profile',
        ]);

        Post::factory(3)->create(['user_id' => $user->id])->each(function ($post) use ($user) {

            Image::factory(rand(1, 3))->create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'type' => 'post',
            ]);
        });
    });
}
}
