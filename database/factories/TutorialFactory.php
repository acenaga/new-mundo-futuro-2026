<?php

namespace Database\Factories;

use App\Enums\PostType;
use App\Models\Category;
use App\Models\Tutorial;

class TutorialFactory extends PostFactory
{
    protected $model = Tutorial::class;

    public function definition(): array
    {
        $category = Category::where('slug', 'tutoriales')->first()
            ?? Category::factory()->create(['slug' => 'tutoriales', 'name' => 'Tutoriales']);

        return array_merge(parent::definition(), [
            'type' => PostType::Tutorial,
            'category_id' => $category->id,
        ]);
    }
}
