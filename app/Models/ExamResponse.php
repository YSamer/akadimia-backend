<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResponse extends Model
{
    protected $fillable = [
        'exam_id',
        'question_id',
        'response',
        'user_id',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getParsedResponseAttribute()
    {
        $questionType = $this->question->type;

        if ($questionType === 'checkbox') {
            return json_decode($this->response, true);
        }

        return $this->response;
    }
}
