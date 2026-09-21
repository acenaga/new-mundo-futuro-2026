<?php

namespace App\Models;

use Database\Factories\DeveloperResourceCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeveloperResourceCategory extends Model
{
    /** @use HasFactory<DeveloperResourceCategoryFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'icon', 'sort_order'];

    public function resources(): HasMany
    {
        return $this->hasMany(DeveloperResource::class);
    }
}
