<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WirdDone extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'wird_id',
        'is_completed',
        'score',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_id' => 'integer',
        'wird_id' => 'integer',
        'is_completed' => 'boolean',
        'score' => 'decimal:2',
    ];

    /**
     * Get the user that completed the wird.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wird associated with this completion record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wird()
    {
        return $this->belongsTo(Wird::class);
    }

    public function getGradeAttribute()
    {
        return $this->wird && $this->is_completed
            ? ($this->score ?? $this->wird->grade)
            : 0;
    }
}
