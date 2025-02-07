<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupWird extends Model
{
    use HasFactory;

    protected $table = 'group_wirds';

    protected $fillable = [
        'group_id',
        'date',
        'hifz_page',
        'tilawah_juz',
        'sama_hizb',
        'weekly_tahder_from',
        'tajweed_dars',
        'tafseer_dars',
        'sard_shikh',
        'sard_rafiq',
        'hifz_tohfa_from',
    ];
    protected $appends = [
        'weekly_tahder_to',
        // 'hifz_tohfa_to',
    ];

    protected $casts = [
        'date' => 'date',
        'sard_shikh' => 'array',
        'sard_rafiq' => 'array',
    ];

    public function getWeeklyTahderToAttribute()
    {
        return $this->weekly_tahder_from ? min($this->weekly_tahder_from === 1 ? 6 : $this->weekly_tahder_from + 5, 604) : null;
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
