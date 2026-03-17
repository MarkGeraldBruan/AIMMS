<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'can_create',
        'can_read',
        'can_update',
        'can_delete',
        'can_stock_in',
        'can_stock_out',
        'avatar',
        'logo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['created_date', 'avatar_url', 'logo_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'can_create' => 'boolean',
            'can_read' => 'boolean',
            'can_update' => 'boolean',
            'can_delete' => 'boolean',
            'can_stock_in' => 'boolean',
            'can_stock_out' => 'boolean',
        ];
    }

    /**
     * Get formatted created date
     */
    public function getCreatedDateAttribute()
    {
        return $this->created_at->format('d F, Y');
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return \Storage::url($this->avatar);
        }

        return asset('assets/img/noprofile.jpg');
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return \Storage::url($this->logo);
        }

        return asset('assets/img/atirtc1logo.jpg');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has permission to perform an action
     */
    public function hasPermission(string $permission): bool
    {
        // Admins can do everything
        if ($this->isAdmin()) {
            return true;
        }

        // Check specific permission
        return match($permission) {
            'create' => $this->can_create,
            'read' => $this->can_read,
            'update' => $this->can_update,
            'delete' => $this->can_delete,
            'stock_in' => $this->can_stock_in,
            'stock_out' => $this->can_stock_out,
            default => false,
        };
    }

    /**
     * Relationships
     */
    public function helpRequests()
    {
        return $this->hasMany(HelpRequest::class);
    }

    public function assignedHelpRequests()
    {
        return $this->hasMany(HelpRequest::class, 'assigned_to');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false);
    }
}
