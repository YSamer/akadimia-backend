<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupWirdConfig extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id',
        'title',
        'description',
        'section_type',
        'wird_type',
        'under_wird',
        'grade',
        'sanction',
        'is_changed',
        'from',
        'to',
        'start_from',
        'end_to',
        'change_value',
        'repeated_from_list',
        'days',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_changed' => 'boolean',
        'days' => 'array',
    ];

    /**
     * Get the group associated with this configuration.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the parent wird if this is under another wird.
     */
    public function parentWird()
    {
        return $this->belongsTo(self::class, 'under_wird');
    }

    /**
     * Get the child wirds under this wird.
     */
    public function childWirds()
    {
        return $this->hasMany(self::class, 'under_wird');
    }

    /**
     * Get the list associated with repeated configurations.
     */
    public function repeatedFromList()
    {
        return $this->belongsTo(ListModel::class, 'repeated_from_list');
    }
}
