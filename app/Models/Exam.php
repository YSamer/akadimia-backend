<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'admin_id',
        'forwardable_type',
        'forwardable_id',
        'start_time',
        'end_time',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function examResponses()
    {
        return $this->hasMany(ExamResponse::class);
    }

    public function forwardable()
    {
        return $this->morphTo();
    }
}
