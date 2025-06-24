<?php

namespace App\Enums;

enum ActivityType: string
{
    case RUN = 'run';
    case WALK = 'walk';
    case SWIMMING = 'swimming';
    case CYCLING = 'cycling';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function rules(): string
    {
        return 'in:' . implode(',', self::values());
    }
}
