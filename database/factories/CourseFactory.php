<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /** @var array<int, string> */
    private static array $titles = [
        'Mastering Next.js 14 & Edge Computing',
        'WebGL y Three.js: La Web Visual',
        'Ética Hacker para la Web Moderna',
        'Micro-Frontends con Module Federation',
        'Rust para Desarrolladores Web',
        'IA Generativa en el Frontend',
        'Svelte 5 y el Renacimiento de la Web',
        'Deployments Zero-Downtime con Kubernetes',
    ];

    private static int $titleIndex = 0;

    public function definition(): array
    {
        $title = self::$titles[self::$titleIndex % count(self::$titles)];
        self::$titleIndex++;

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->paragraph(3),
            'image_path' => null,
            'status' => 'published',
            'is_premium' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_premium' => true,
        ]);
    }
}
