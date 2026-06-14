<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6, true);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'type' => PostType::Article,
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

    public function article(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PostType::Article,
        ]);
    }

    public function tutorial(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PostType::Tutorial,
            'category_id' => null,
        ]);
    }
}
