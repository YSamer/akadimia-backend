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

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
