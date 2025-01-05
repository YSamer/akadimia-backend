<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Batch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'submission_date',
        'start_date',
        'max_number',
        'gender',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submission_date' => 'date',
        'start_date' => 'date',
        'max_number' => 'integer',
    ];

    /**
     * The achievements that belong to the batch.
     */
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'batch_achievements');
    }

    /**
     * Get the groups associated with the batch.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    // get numder members for all groups in the batch
    public function allMembers()
    {
        return GroupMember::whereHas('group', function ($query) {
            $query->where('batch_id', $this->id);
        })->get();
    }

    // get numder members for users in all groups in the batch
    public function usersMembers()
    {
        return $this->allMembers()->where('member_type', 'App\Models\User');
    }

    public function exams()
    {
        return $this->morphMany(Exam::class, 'forwardable');
    }
}
