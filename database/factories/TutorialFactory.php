<?php

namespace Database\Factories;

use App\Enums\PostType;
use App\Models\Category;
use App\Models\Tutorial;

class TutorialFactory extends PostFactory
{
    protected $model = Tutorial::class;

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Tutorial $tutorial) {
            $category = Category::where('slug', 'tutoriales')->first();

            if (! $category) {
                throw new \RuntimeException('La categoría obligatoria "tutoriales" no existe en la base de datos.');
            }

            $tutorial->category()->associate($category);
            $tutorial->saveQuietly();
        });
    }

    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'type' => PostType::Tutorial,
            'category_id' => null,
        ]);
    }
}
