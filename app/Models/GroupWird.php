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
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
