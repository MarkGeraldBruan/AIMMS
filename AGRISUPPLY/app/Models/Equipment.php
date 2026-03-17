<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipment';

    protected $fillable = [
        'property_number',
        'article',
        'classification',
        'description',
        'unit_of_measurement',
        'unit_value',
        'condition',
        'acquisition_date',
        'location',
        'responsible_person',
        'remarks',
        'user_id'
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'unit_value' => 'decimal:2'
    ];

    /**
     * Get the user who created this equipment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate property number in format YYYY-MM-DD-ID
     */
    public static function generatePropertyNumber()
    {
        $date = Carbon::now()->format('Y-m-d');
        $lastEquipment = self::whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();
        
        $nextId = $lastEquipment ? $lastEquipment->id + 1 : 1;
        
        return $date . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get formatted acquisition date
     */
    public function getFormattedAcquisitionDateAttribute()
    {
        return $this->acquisition_date ? $this->acquisition_date->format('M d, Y') : 'N/A';
    }

    /**
     * Check if equipment is serviceable
     */
    public function isServiceable()
    {
        return $this->condition === 'Serviceable';
    }

    /**
     * Get condition badge class
     */
    public function getConditionBadgeClassAttribute()
    {
        return $this->condition === 'Serviceable' ? 'status-serviceable' : 'status-unserviceable';
    }

    /**
     * Scope for filtering by condition
     */
    public function scopeByCondition($query, $condition)
    {
        if ($condition) {
            return $query->where('condition', $condition);
        }
        return $query;
    }

    /**
     * Scope for searching
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('article', 'like', "%{$search}%")
                  ->orWhere('classification', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('property_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('responsible_person', 'like', "%{$search}%")
                  ->orWhere('id', '=', $search);
            });
        }
        return $query;
    }
}