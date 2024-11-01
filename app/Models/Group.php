<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_id',
        'name',
        'image',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'batch_id' => 'integer',
    ];

    /**
     * Get the batch that the group belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Get the group configurations associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupConfigs()
    {
        return $this->hasMany(GroupConfig::class);
    }

    /**
     * Get the wirds associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wirds()
    {
        return $this->hasMany(Wird::class);
    }

    /**
     * Get the users associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'member', 'group_members', 'group_id', 'member_id')
            ->withTimestamps();
    }

    /**
     * Get the admins associated with the group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function admins()
    {
        return $this->morphedByMany(Admin::class, 'member', 'group_members', 'group_id', 'member_id')
            ->withTimestamps();
    }
}
