<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeletedSupply extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'supply_id',
        'name',
        'description',
        'quantity',
        'unit_price',
        'unit',
        'category',
        'supplier',
        'purchase_date',
        'minimum_stock',
        'notes',
        'total_value',
        'reason',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'unit_price' => 'decimal:2',
        'total_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user who deleted this item
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%")
              ->orWhere('supplier', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}