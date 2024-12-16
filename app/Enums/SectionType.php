<?php

namespace App\Enums;

enum SectionType: string
{
    case JUZ = 'juz';
    case HIZB = 'hizb';
    case QUARTER = 'quarter';
    case SURAH = 'surah';
    case PAGE = 'page';
    case AYAH = 'ayah';
    case POETRY_LINE = 'poetry_line';

    /**
     * Get the Arabic name for each type.
     */
    public function arabicName(): string
    {
        return match ($this) {
            self::JUZ => 'جزء',
            self::HIZB => 'حزب',
            self::QUARTER => 'ربع',
            self::SURAH => 'سورة',
            self::PAGE => 'وجه',
            self::AYAH => 'آية',
            self::POETRY_LINE => 'بيت',
        };
    }
}
