<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'urls' => 'array',
    // ];

    /**
     * The batches that belong to the achievement.
     */
    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_achievements');
    }

    public function urls()
    {
        return $this->morphMany(Url::class, 'urlable');
    }
}
