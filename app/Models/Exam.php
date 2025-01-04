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

    public function forMe()
    {
        if ($this->forwardable_type === 'App\\Models\\User' && $this->forwardable_id === Auth::id()) {
            return true;
        }

        if ($this->forwardable_type === 'App\\Models\\Group' && $this->isInGroup($this->forwardable_id)) {
            return $this->isInGroup($this->forwardable_id);
        }

        if ($this->forwardable_type === 'App\\Models\\Batch' && $this->isInBatch($this->forwardable_id)) {
            return $this->isInBatch($this->forwardable_id);
        }

        return false;
    }

    protected function isInGroup($groupId)
    {
        return Group::find($groupId)->users()->where('member_id', Auth::id())->count() > 0;
    }

    protected function isInBatch($batchId)
    {
        return Batch::find($batchId)->usersMembers()->where('member_id', Auth::id())->count() > 0;
    }
}
