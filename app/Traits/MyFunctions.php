<?php

namespace App\Traits;

trait MyFunctions
{

    function splitConsecutiveNumbers($numbers)
    {
        $groups = [];
        $currentGroup = [];

        // sort($numbers);

        foreach ($numbers as $number) {
            if (empty($currentGroup) || $number == end($currentGroup) + 1) {
                $currentGroup[] = $number;
            } else {
                $groups[] = $currentGroup;
                $currentGroup = [$number];
            }
        }

        if (!empty($currentGroup)) {
            $groups[] = $currentGroup;
        }

        return $groups;
    }


    public function numberToArabicOrdinal($number, $type = 'Male')
    {
        return $type === 'Male' ?
            $this->numberToArabicOrdinalMale($number)
            : $this->numberToArabicOrdinalFemale($number);
    }
    public function numberToArabicOrdinalMale($number)
    {
        $arabicNumbers = [
            1 => 'الأول',
            2 => 'الثاني',
            3 => 'الثالث',
            4 => 'الرابع',
            5 => 'الخامس',
            6 => 'السادس',
            7 => 'السابع',
            8 => 'الثامن',
            9 => 'التاسع',
            10 => 'العاشر',
            11 => 'الحادي عشر',
            12 => 'الثاني عشر',
            13 => 'الثالث عشر',
            14 => 'الرابع عشر',
            15 => 'الخامس عشر',
            16 => 'السادس عشر',
            17 => 'السابع عشر',
            18 => 'الثامن عشر',
            19 => 'التاسع عشر',
            20 => 'العشرون',
        ];

        if ($number <= 20) {
            return $arabicNumbers[$number] ?? '';
        }

        $tens = (int) ($number / 10) * 10;
        $units = $number % 10;

        $arabicTens = [
            20 => 'العشرون',
            30 => 'الثلاثون',
            40 => 'الأربعون',
            50 => 'الخمسون',
            60 => 'الستون',
            70 => 'السبعون',
            80 => 'الثمانون',
            90 => 'التسعون'
        ];

        if ($units === 0) {
            return $arabicTens[$tens];
        }

        return ($units == 1 ? 'الحادي' : $arabicNumbers[$units]) . ' و' . $arabicTens[$tens];
    }

    function numberToArabicOrdinalFemale($number)
    {
        $arabicNumbers = [
            1 => 'الأولى',
            2 => 'الثانية',
            3 => 'الثالثة',
            4 => 'الرابعة',
            5 => 'الخامسة',
            6 => 'السادسة',
            7 => 'السابعة',
            8 => 'الثامنة',
            9 => 'التاسعة',
            10 => 'العاشرة',
            11 => 'الحادية عشرة',
            12 => 'الثانية عشرة',
            13 => 'الثالثة عشرة',
            14 => 'الرابعة عشرة',
            15 => 'الخامسة عشرة',
            16 => 'السادسة عشرة',
            17 => 'السابعة عشرة',
            18 => 'الثامنة عشرة',
            19 => 'التاسعة عشرة',
            20 => 'العشرون'
        ];

        if ($number <= 20) {
            return $arabicNumbers[$number];
        }

        $tens = (int) ($number / 10) * 10;
        $units = $number % 10;

        $arabicTens = [
            20 => 'العشرون',
            30 => 'الثلاثون',
            40 => 'الأربعون',
            50 => 'الخمسون',
            60 => 'الستون',
            70 => 'السبعون',
            80 => 'الثمانون',
            90 => 'التسعون'
        ];

        if ($units === 0) {
            return $arabicTens[$tens];
        }

        return ($units == 1 ? 'الحادية' : $arabicNumbers[$units]) . ' و' . $arabicTens[$tens];
    }
}