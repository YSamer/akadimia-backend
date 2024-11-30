<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Batch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'submission_date',
        'start_date',
        'max_number',
        'gender',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submission_date' => 'date',
        'start_date' => 'date',
        'max_number' => 'integer',
    ];

    /**
     * The achievements that belong to the batch.
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'batch_achievements');
    }

    /**
     * Get the groups associated with the batch.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }


    function numberToArabicOrdinal()
    {
        $number = $this->id;

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
