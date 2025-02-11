<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWirdsDone extends Model
{
    use HasFactory;

    protected $table = 'user_wirds_dones';

    protected $fillable = [
        'group_id',
        'user_id',
        'date',
        'tilawah_juz_done',
        'sama_hizb_done',
        'weekly_tahder_done',
        'hifz_night_tahder_done',
        'hifz_before_tahder_done',
        'hifz_tafser_done',
        'hifz_tadabor_done',
        'hifz_waqfa_text',
        'hifz_dabt_tilawah_done',
        'salat_hifz_done',
        'halaqah_grade',
        'sard_shikh_grade',
        'sard_rafiq_grade',
        'tafseer_dars_done',
        'tajweed_dars_done',
        'fwaed_text',
    ];

    protected $casts = [
        'date' => 'date',
        'tilawah_juz_done' => 'boolean',
        'sama_hizb_done' => 'boolean',
        'weekly_tahder_done' => 'boolean',
        'hifz_night_tahder_done' => 'boolean',
        'hifz_before_tahder_done' => 'boolean',
        'hifz_tafser_done' => 'boolean',
        'hifz_tadabor_done' => 'boolean',
        'hifz_dabt_tilawah_done' => 'boolean',
        'salat_hifz_done' => 'boolean',
        'halaqah_grade' => 'integer',
        'sard_shikh_grade' => 'integer',
        'sard_rafiq_grade' => 'integer',
        'tafseer_dars_done' => 'boolean',
        'tajweed_dars_done' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
