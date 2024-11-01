<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupConfigDay extends Model
{
    protected $fillable = [
        'group_config_id',
        'day',
    ];

    public function groupConfig()
    {
        return $this->belongsTo(GroupConfig::class);
    }
}
