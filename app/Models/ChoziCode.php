<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChoziCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'broker_id',
        'code',
        'commission_rate',
        'usage_count',
        'is_active',
        'expires_at',
        'description',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
        'expires_at' => 'date',
    ];

    // Relationships
    public function broker()
    {
        return $this->belongsTo(User::class, 'broker_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helper methods
    public function isValid()
    {
        return $this->is_active && 
               ($this->expires_at === null || $this->expires_at > now());
    }

    public function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());
        
        $this->code = $code;
        return $code;
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function getTotalCommissionEarned()
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('broker_commission');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }
}
