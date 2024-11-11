<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BatchApply extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'batch_id',
        'status',
        'achievement_ids',
        'notes',
    ];

    protected $casts = [
        'achievement_ids' => 'array',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
}
