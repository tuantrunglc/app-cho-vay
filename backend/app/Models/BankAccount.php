<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BankAccount Model
 * 
 * Represents user's linked bank accounts
 */
class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_code',
        'bank_name',
        'account_number',
        'account_name',
        'is_default',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'verified_at' => 'datetime',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();
        
        // Ensure only one default bank account per user
        static::creating(function ($bankAccount) {
            if ($bankAccount->is_default) {
                self::where('user_id', $bankAccount->user_id)
                    ->update(['is_default' => false]);
            }
        });

        static::updating(function ($bankAccount) {
            if ($bankAccount->is_default && $bankAccount->isDirty('is_default')) {
                self::where('user_id', $bankAccount->user_id)
                    ->where('id', '!=', $bankAccount->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the user this bank account belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if bank account is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if bank account is verified
     */
    public function isVerified(): bool
    {
        return $this->status === 'active' && $this->verified_at !== null;
    }

    /**
     * Check if bank account is default
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Set as default bank account
     */
    public function setAsDefault(): void
    {
        // Remove default from other accounts
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
            
        $this->update(['is_default' => true]);
    }

    /**
     * Verify the bank account
     */
    public function verify(): void
    {
        $this->update([
            'status' => 'active',
            'verified_at' => now(),
        ]);
    }

    /**
     * Block the bank account
     */
    public function block(): void
    {
        $this->update([
            'status' => 'blocked',
            'is_default' => false,
        ]);
    }

    /**
     * Get masked account number
     */
    public function getMaskedAccountNumberAttribute(): string
    {
        $accountNumber = $this->account_number;
        $length = strlen($accountNumber);
        
        if ($length <= 4) {
            return $accountNumber;
        }
        
        return str_repeat('*', $length - 4) . substr($accountNumber, -4);
    }

    /**
     * Get bank logo URL
     */
    public function getBankLogoAttribute(): string
    {
        $logos = [
            'VCB' => '/images/banks/vcb.png',
            'TCB' => '/images/banks/tcb.png',
            'VTB' => '/images/banks/vtb.png',
            'BIDV' => '/images/banks/bidv.png',
            'MB' => '/images/banks/mb.png',
            'ACB' => '/images/banks/acb.png',
            'VPB' => '/images/banks/vpb.png',
            'TPB' => '/images/banks/tpb.png',
            'STB' => '/images/banks/stb.png',
            'HDB' => '/images/banks/hdb.png',
        ];
        
        return $logos[$this->bank_code] ?? '/images/banks/default.png';
    }

    /**
     * Get supported banks list
     */
    public static function getSupportedBanks(): array
    {
        return [
            'VCB' => 'Vietcombank',
            'TCB' => 'Techcombank',
            'VTB' => 'Vietinbank',
            'BIDV' => 'BIDV',
            'MB' => 'MB Bank',
            'ACB' => 'ACB',
            'VPB' => 'VPBank',
            'TPB' => 'TPBank',
            'STB' => 'Sacombank',
            'HDB' => 'HDBank',
        ];
    }

    /**
     * Scope for active bank accounts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for verified bank accounts
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'active')
                    ->whereNotNull('verified_at');
    }

    /**
     * Scope for default bank account
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for specific bank
     */
    public function scopeOfBank($query, string $bankCode)
    {
        return $query->where('bank_code', $bankCode);
    }
}