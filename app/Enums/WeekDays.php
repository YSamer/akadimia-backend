<?php

namespace App\Enums;

enum WeekDays: string
{
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';

    public function arabicName(): string
    {
        return match ($this) {
            self::SATURDAY => 'السبت',
            self::SUNDAY => 'الأحد',
            self::MONDAY => 'الإثنين',
            self::TUESDAY => 'الثلاثاء',
            self::WEDNESDAY => 'الأربعاء',
            self::THURSDAY => 'الخميس',
            self::FRIDAY => 'الجمعة',
        };
    }

    /**
     * Exclude specific weekdays from the list.
     *
     * @param array $excludedDays
     * @return array
     */
    public static function excludeDays(array $excludedDays): array
    {
        return array_values(array_filter(
            self::cases(),
            fn(WeekDays $day) => !in_array($day->value, $excludedDays, true)
        ));
    }

    /**
     * Get a list of all weekdays in their string representation.
     *
     * @return array
     */
    public static function values(): array
    {
        return array_map(
            fn(WeekDays $day) => $day->value,
            self::cases()
        );
    }
}
