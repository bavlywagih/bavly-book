<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
public function definition(): array
{
    $types = ['cover', 'profile', 'post'];
    $type = $this->faker->randomElement($types);

    $folder = match ($type) {
        'cover' => 'cover_photos/',
        'profile' => 'profile_photos/',
        'post' => 'post_images/',
    };

    // استخدم أسماء حقيقية من الصور الموجودة
    $number = rand(1, 10); // assuming you have 10 صور لكل نوع
    $filename = $folder . $number . '.jpg'; // or png/webp لو عندك منهم فعلاً

    return [
        'user_id' => \App\Models\User::factory(),
        'post_id' => $type === 'post' ? \App\Models\Post::factory() : null,
        'type' => $type,
        'path' => $filename,
        'is_current' => false,
        'created_at' => $this->faker->dateTimeBetween('-2 months', 'now'),

    ];
}
}
