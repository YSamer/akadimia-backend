<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListSection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'list_id',
        'title',
        'file',
        'url',
    ];

    /**
     * Get the list that owns the section.
     */
    public function list()
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }
}
