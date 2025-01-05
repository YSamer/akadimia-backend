<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'amount',
        'image',
        'is_confirm',
        'confirmed_by',
    ];

    protected $casts = [
        'is_confirm' => 'boolean',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'confirmed_by');
    }
}
