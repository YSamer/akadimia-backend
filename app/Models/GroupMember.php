<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group_id',
        'member_id',
        'member_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'group_id' => 'integer',
        'member_id' => 'integer',
    ];

    /**
     * Get the owning member model (User or Admin).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function member()
    {
        return $this->morphTo();
    }

    /**
     * Get the group that the member belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the member type as a human-readable label.
     *
     * @return string
     */
    public function memberType()
    {
        $map = [
            'App\\Models\\User' => 'student',
            'App\\Models\\Teacher' => 'teacher',
            'App\\Models\\Admin' => 'admin',
        ];

        return $map[$this->member_type] ?? 'unknown';
    }
}
