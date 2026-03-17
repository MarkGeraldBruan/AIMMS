<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Supplies extends Model
{
    protected $fillable = [
        'name',
        'description',
        'quantity',
        'unit_price',
        'unit',
        'category',
        'supplier',
        'purchase_date',
        'minimum_stock',
        'notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'unit_price' => 'decimal:2'
    ];

    /**
     * Get the stock movements for the supply
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Check if supply is low stock
    public function isLowStock()
    {
        return $this->quantity <= $this->minimum_stock;
    }

    // Calculate total value
    public function getTotalValueAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    // Format purchase date
    public function getFormattedPurchaseDateAttribute()
    {
        return $this->purchase_date ? $this->purchase_date->format('M d, Y') : 'N/A';
    }

    // Scope for low stock items
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= minimum_stock');
    }

    // Scope for search
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('category', 'like', "%{$term}%")
              ->orWhere('supplier', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhere('id', '=', $term);
        });
    }
}