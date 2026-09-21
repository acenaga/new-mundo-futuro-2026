<?php

namespace Database\Factories;

use App\Models\DeveloperResourceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DeveloperResourceCategory>
 */
class DeveloperResourceCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'icon' => 'code-bracket',
            'sort_order' => fake()->numberBetween(1, 20),
        ];
    }
}
