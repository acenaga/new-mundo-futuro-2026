<?php

namespace App\Rules;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ScheduledAtHalfHour implements ValidationRule
{
    /**
     * Siguiente slot de agenda alineado a :00 o :30.
     */
    public static function nextSlot(): CarbonImmutable
    {
        $slot = CarbonImmutable::now()->startOfMinute();
        $minutes = (int) $slot->format('i');
        $minutesToNext = $minutes < 30 ? 30 - $minutes : 60 - $minutes;

        return $slot->addMinutes($minutesToNext)->setSecond(0);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof CarbonInterface) {
            return;
        }

        if ($value->lessThanOrEqualTo(now())) {
            $fail(__('validation.scheduled_must_be_future'));

            return;
        }

        if (! in_array((int) $value->format('i'), [0, 30], true) || $value->format('s') !== '00') {
            $fail(__('validation.scheduled_half_hour'));
        }
    }
}
