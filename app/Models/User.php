<?php

namespace App\Models;


use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'image',
        'gender',
        'telegram',
        'birth_date',
        'role',
        'is_active',
        'device_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'device_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the groups the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function groups()
    {
        return $this->morphToMany(Group::class, 'member', 'group_members', 'member_id', 'group_id')
            ->withTimestamps();
    }

    /**
     * Get all wird completions associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wirdDones()
    {
        return $this->hasMany(WirdDone::class);
    }

    /**
     * Check if the admin has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Scope a query to only include admins with a specific role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Check if the user is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the batch applies for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applies()
    {
        return $this->hasMany(BatchApply::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function exams()
    {
        return $this->morphMany(Exam::class, 'forwardable');
    }

    public function allNotifications()
    {
        return $this->morphMany(Notification::class, 'user');
    }
    
    public function unreadedNotifications()
    {
        return $this->allNotifications()->where('read_at', null);
    }
}
