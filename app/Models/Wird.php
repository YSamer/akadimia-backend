<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wird extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_id',
        'date',
        'type',
        'amount',
        'score',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'group_id' => 'integer',
        'date' => 'date',
        'amount' => 'integer',
        'score' => 'decimal:2',
    ];

    /**
     * Get the group that this wird belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the instances of WirdDone associated with this wird.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wirdDones()
    {
        return $this->hasMany(WirdDone::class);
    }
}
