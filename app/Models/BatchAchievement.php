<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchAchievement extends Model
{
    protected $fillable = [
        'batch_id',
        'achievement_id',
    ];

    /**
     * Get the batch that owns the BatchAchievement.
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the achievement that owns the BatchAchievement.
     */
    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }
}
