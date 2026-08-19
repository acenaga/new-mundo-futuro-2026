<?php

namespace App\Enums;

enum PostType: string
{
    case Article = 'article';
    case Tutorial = 'tutorial';

    public function label(): string
    {
        return match ($this) {
            self::Article => 'Artículo',
            self::Tutorial => 'Tutorial',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Article => 'info',
            self::Tutorial => 'success',
        };
    }
}
