<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

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

    public function userGrade()
    {
        $responses = $this->examResponses()->where('user_id', Auth::id());
        return $responses->exists() ? $responses->with('question')
            ->get()
            ->sum(function ($response) {
                return $response->question->grade ?? 0;
            }) : null;
    }
}
