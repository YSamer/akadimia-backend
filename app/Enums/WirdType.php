<?php

namespace App\Enums;

enum WirdType: string
{
    case HIFZ = 'hifz';                // حفظ
    case MURAJAA = 'murajaa';          // مراجعه
    case TILAWAH = 'tilawah';          // تلاوة
    case SAMA = 'sama';                // سماع
    case DARS = 'dars';                // درس
    case QIRAAH = 'qiraah';            // قرآءه
    case TILAWAH_OR_SAMA = 'tilawah_or_sama'; // تلاوه او سماع
    case KITABAH = 'kitabah';          // كتابه
    case SARD = 'sard';                // سرد
    case HALAQAH = 'halaqah';          // حلقه
    case SALAH_MAHSOOF = 'salah_mahsoof'; // الصلاة بالمحفوظ
    case MUTOON = 'mutoon';            // متون

    public function label(): string
    {
        return match ($this) {
            self::HIFZ => 'حفظ',
            self::MURAJAA => 'مراجعه',
            self::TILAWAH => 'تلاوة',
            self::SAMA => 'سماع',
            self::DARS => 'درس',
            self::QIRAAH => 'قرآءه',
            self::TILAWAH_OR_SAMA => 'تلاوه او سماع',
            self::KITABAH => 'كتابه',
            self::SARD => 'سرد',
            self::HALAQAH => 'حلقه',
            self::SALAH_MAHSOOF => 'الصلاة بالمحفوظ',
            self::MUTOON => 'متون',
        };
    }
}

