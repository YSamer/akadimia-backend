<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Halaqah extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'teacher_id',
        'target_type',
        'target_id',
        'duration_hours',
        'duration_minutes',
        'date',
    ];

    public function target()
    {
        return $this->morphTo();
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
