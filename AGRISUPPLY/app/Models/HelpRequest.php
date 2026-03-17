<?php

// app/Models/HelpRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['created_date', 'priority_color', 'status_color'];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    public function getCreatedDateAttribute()
    {
        return $this->created_at
            ->timezone('Asia/Manila')
            ->format('F d, Y h:i A');
    }

    public function getUpdatedDateAttribute()
    {
        return $this->updated_at
            ->timezone('Asia/Manila')
            ->format('F d, Y h:i A');
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_progress' => 'info',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary'
        };
    }
}

// app/Models/Notification.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    protected $appends = ['created_date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCreatedDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}