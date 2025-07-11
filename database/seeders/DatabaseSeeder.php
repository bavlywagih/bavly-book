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
    // أنشئ 10 مستخدمين
    User::factory(50)->create()->each(function ($user) {

        // لكل مستخدم، أضف:
        // صورة غلاف حالية
        $cover = Image::factory()->create([
            'user_id' => $user->id,
            'type' => 'cover',
            'is_current' => true,
        ]);

        // صور غلاف سابقة
        Image::factory(2)->create([
            'user_id' => $user->id,
            'type' => 'cover',
        ]);

        // صورة بروفايل حالية
        $profile = Image::factory()->create([
            'user_id' => $user->id,
            'type' => 'profile',
            'is_current' => true,
        ]);

        // صور بروفايل سابقة
        Image::factory(2)->create([
            'user_id' => $user->id,
            'type' => 'profile',
        ]);

        // لكل مستخدم 3 بوستات
        Post::factory(3)->create(['user_id' => $user->id])->each(function ($post) use ($user) {
            // كل بوست فيه 1-3 صور
            Image::factory(rand(1, 3))->create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'type' => 'post',
            ]);
        });
    });
}
}
