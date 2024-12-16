<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wird extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'wirds';

    // Define the fillable fields (to protect against mass assignment)
    protected $fillable = [
        'group_id',
        'group_wird_config_id',
        'date',
        'title',
        'start_from',
        'end_to',
        'file_path',
        'url',
    ];

    // Define the relationships

    /**
     * Get the group that owns the Wird.
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the group config associated with the Wird.
     */
    public function groupWirdConfig()
    {
        return $this->belongsTo(GroupWirdConfig::class, 'group_wird_config_id');
    }
}
