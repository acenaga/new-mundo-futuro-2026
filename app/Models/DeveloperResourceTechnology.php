<?php

namespace App\Models;

use Database\Factories\DeveloperResourceTechnologyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DeveloperResourceTechnology extends Model
{
    /** @use HasFactory<DeveloperResourceTechnologyFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(DeveloperResource::class);
    }
}
