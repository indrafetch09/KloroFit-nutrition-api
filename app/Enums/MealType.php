<?php

namespace App\Enums;

enum MealType: string
{
    case BREAKFAST = 'breakfast';
    case LUNCH = 'lunch';
    case DINNER = 'dinner';
    case SNACK = 'snack';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function rules(): string
    {
        return 'in:' . implode(',', self::values()) . '|nullable';
    }
}
