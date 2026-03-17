<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAuditTrail extends Model
{
    protected $fillable = [
        'user_id',
        'supply_id',
        'stock_movement_id',
        'action_type',
        'quantity',
        'balance_before',
        'balance_after',
        'reference',
        'notes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the supply that was affected
     */
    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supplies::class);
    }

    /**
     * Get the related stock movement
     */
    public function stockMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class);
    }

    /**
     * Scope for filtering by action type
     */
    public function scopeByActionType($query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by supply
     */
    public function scopeBySupply($query, $supplyId)
    {
        return $query->where('supply_id', $supplyId);
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Get formatted action type
     */
    public function getFormattedActionTypeAttribute()
    {
        return $this->action_type === 'stock_in' ? 'Stock In' : 'Stock Out';
    }

    /**
     * Get action badge class
     */
    public function getActionBadgeClassAttribute()
    {
        return $this->action_type === 'stock_in' ? 'badge-success' : 'badge-danger';
    }
}