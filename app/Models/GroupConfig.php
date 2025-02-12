<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupConfig extends Model
{
    use HasFactory;

    protected $table = 'group_configs';

    protected $fillable = [
        'group_id',
        'tilawah_juz_grade',
        'tilawah_juz_sanction',
        'sama_hizb_grade',
        'sama_hizb_sanction',
        'weekly_tahder_grade',
        'weekly_tahder_sanction',
        'hifz_night_tahder_grade',
        'hifz_night_tahder_sanction',
        'hifz_before_tahder_grade',
        'hifz_before_tahder_sanction',
        'hifz_tafser_grade',
        'hifz_tafser_sanction',
        'hifz_tadabor_grade',
        'hifz_tadabor_sanction',
        'hifz_waqfa_grade',
        'hifz_waqfa_sanction',
        'hifz_dabt_tilawah_grade',
        'hifz_dabt_tilawah_sanction',
        'salat_hifz_grade',
        'salat_hifz_sanction',
        'halaqah_grade',
        'halaqah_sanction',
        'sard_shikh_grade',
        'sard_shikh_sanction',
        'sard_rafiq_grade',
        'sard_rafiq_sanction',
        'tafseer_dars_grade',
        'tafseer_dars_sanction',
        'tajweed_dars_grade',
        'tajweed_dars_sanction',
        'fwaed_grade',
        'fwaed_sanction',
        'saturday',
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'sard_shikh',
        'sard_rafiq',
        'tohfa',
        'hifz_start_from',
        'halaqah_time',
    ];

    protected $casts = [
        'tilawah_juz_grade' => 'integer',
        'tilawah_juz_sanction' => 'integer',
        'sama_hizb_grade' => 'integer',
        'sama_hizb_sanction' => 'integer',
        'weekly_tahder_grade' => 'integer',
        'weekly_tahder_sanction' => 'integer',
        'hifz_night_tahder_grade' => 'integer',
        'hifz_night_tahder_sanction' => 'integer',
        'hifz_before_tahder_grade' => 'integer',
        'hifz_before_tahder_sanction' => 'integer',
        'hifz_tafser_grade' => 'integer',
        'hifz_tafser_sanction' => 'integer',
        'hifz_tadabor_grade' => 'integer',
        'hifz_tadabor_sanction' => 'integer',
        'hifz_waqfa_grade' => 'integer',
        'hifz_waqfa_sanction' => 'integer',
        'hifz_dabt_tilawah_grade' => 'integer',
        'hifz_dabt_tilawah_sanction' => 'integer',
        'salat_hifz_grade' => 'integer',
        'salat_hifz_sanction' => 'integer',
        'halaqah_grade' => 'integer',
        'halaqah_sanction' => 'integer',
        'sard_shikh_grade' => 'integer',
        'sard_shikh_sanction' => 'integer',
        'sard_rafiq_grade' => 'integer',
        'sard_rafiq_sanction' => 'integer',
        'tafseer_dars_grade' => 'integer',
        'tafseer_dars_sanction' => 'integer',
        'tajweed_dars_grade' => 'integer',
        'tajweed_dars_sanction' => 'integer',
        'fwaed_grade' => 'integer',
        'fwaed_sanction' => 'integer',
        'tohfa' => 'integer',
        'hifz_start_from' => 'integer',
        'halaqah_time' => 'time',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
