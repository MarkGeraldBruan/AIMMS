<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'status',
        'event_date',
        'created_by'
    ];

    protected $casts = [
        'event_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who created the announcement
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the supplies associated with this announcement
     * FIXED: Added foreignPivotKey and relatedPivotKey to match migration column names
     */
    public function supplies(): BelongsToMany
    {
        return $this->belongsToMany(Supplies::class, 'announcement_supplies', 'announcement_id', 'supply_id')
            ->withPivot(['quantity_needed', 'quantity_used', 'status', 'notes', 'reserved_at', 'used_at'])
            ->withTimestamps();
    }

    /**
     * Check if announcement has supplies attached
     */
    public function hasSupplies(): bool
    {
        return $this->supplies()->exists();
    }

    /**
     * Get total quantity needed for all supplies
     */
    public function getTotalQuantityNeededAttribute(): int
    {
        return $this->supplies()->sum('announcement_supplies.quantity_needed');
    }

    /**
     * Check if all supplies are available
     */
    public function hasAvailableStock(): bool
    {
        foreach ($this->supplies as $supply) {
            if ($supply->quantity < $supply->pivot->quantity_needed) {
                return false;
            }
        }
        return true;
    }
}