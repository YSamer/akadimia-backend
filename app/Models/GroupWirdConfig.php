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
        'is_repeated',
        'is_changed',
        'is_weekly_changed',
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

    /**
     * Get the wirds range
     */
    public function getWirdsRange($start_from, $end_to = null)
    {
        $from = $this->from;
        $to = $this->to;
        $change_value = $this->change_value;

        $end_to = $end_to ?: ($start_from + $change_value - 1);

        if ($start_from < $from || $start_from > $to) {
            $start_from = ($start_from % $to) + ($from - 1) ?: $from;
            // throw new \InvalidArgumentException("The start_from {$start_from} must be between {$from} and {$to} {$this}");
        }

        // Normalize $end_to in case it exceeds $to
        if ($end_to > $to) {
            $end_to = ($end_to % $to) + ($from - 1) ?: $to;
        }

        $result = [];

        if ($start_from <= $end_to) {
            $result = range($start_from, $end_to);
        } else {
            $result = array_merge(
                range($start_from, $to),
                range($from, $end_to)
            );
        }

        return $result;
    }
}