<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Banner Model
 * 
 * Represents promotional banners displayed in the app
 */
class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image_url',
        'link_url',
        'active',
        'order',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Check if banner is active
     */
    public function isActive(): bool
    {
        if (!$this->active) {
            return false;
        }

        $now = now()->toDateString();
        
        if ($this->start_date && $now < $this->start_date) {
            return false;
        }
        
        if ($this->end_date && $now > $this->end_date) {
            return false;
        }
        
        return true;
    }

    /**
     * Activate the banner
     */
    public function activate(): void
    {
        $this->update(['active' => true]);
    }

    /**
     * Deactivate the banner
     */
    public function deactivate(): void
    {
        $this->update(['active' => false]);
    }

    /**
     * Scope for active banners
     */
    public function scopeActive($query)
    {
        $now = now()->toDateString();
        
        return $query->where('active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $now);
                    });
    }

    /**
     * Scope for inactive banners
     */
    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    /**
     * Scope for ordered banners
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope for banners in date range
     */
    public function scopeInDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where(function ($q) use ($startDate) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $startDate);
            });
        }
        
        if ($endDate) {
            $query->where(function ($q) use ($endDate) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $endDate);
            });
        }
        
        return $query;
    }
}