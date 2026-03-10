<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6, true);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(5, true),
            'cover_image_path' => null,
            'status' => PostStatus::Draft,
            'published_at' => null,
            'allow_comments' => true,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Published,
            'published_at' => now(),
        ]);
    }
}
