<?php

namespace App\Enums;

enum DeveloperResourcePricing: string
{
    case Free = 'free';
    case Freemium = 'freemium';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Gratis',
            self::Freemium => 'Freemium',
        };
    }
}
