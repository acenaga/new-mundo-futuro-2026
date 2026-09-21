<?php

namespace Database\Factories;

use App\Enums\DeveloperResourcePricing;
use App\Enums\DeveloperResourceStatus;
use App\Models\DeveloperResource;
use App\Models\DeveloperResourceCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeveloperResource>
 */
class DeveloperResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'user_id' => User::factory(),
            'developer_resource_category_id' => DeveloperResourceCategory::factory(),
            'name' => $name,
            'slug' => str($name)->slug(),
            'external_url' => fake()->unique()->url(),
            'summary' => fake()->sentence(),
            'why_use_it' => fake()->paragraph(),
            'when_not_to_use_it' => fake()->paragraph(),
            'pricing' => DeveloperResourcePricing::Freemium,
            'free_tier_details' => fake()->paragraph(),
            'no_card_required' => true,
            'status' => DeveloperResourceStatus::Draft,
            'last_verified_at' => now(),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => DeveloperResourceStatus::Published,
            'published_at' => now(),
        ]);
    }
}
