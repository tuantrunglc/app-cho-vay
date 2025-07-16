<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Payment Model
 * 
 * Represents loan payments made by customers
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'loan_id',
        'user_id',
        'amount',
        'principal_amount',
        'interest_amount',
        'penalty_amount',
        'payment_method',
        'status',
        'payment_date',
        'due_date',
        'notes',
        'payment_details',
        'transaction_id',
        'processed_at',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'payment_date' => 'date',
        'due_date' => 'date',
        'payment_details' => 'array',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Generate unique payment ID
     */
    public static function generatePaymentId(): string
    {
        $lastPayment = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastPayment ? (int)substr($lastPayment->payment_id, 3) + 1 : 1;
        return 'PAY' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate payment_id
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($payment) {
            if (empty($payment->payment_id)) {
                $payment->payment_id = self::generatePaymentId();
            }
            if (empty($payment->payment_date)) {
                $payment->payment_date = now()->toDateString();
            }
        });
    }

    /**
     * Get the loan this payment belongs to
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * Get the user that made the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark payment as processing
     */
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark payment as completed
     */
    public function markAsCompleted(string $transactionId = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'notes' => $reason,
        ]);
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabel(): string
    {
        $labels = [
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'cash' => 'Tiền mặt',
            'online_banking' => 'Internet Banking',
            'e_wallet' => 'Ví điện tử',
        ];
        
        return $labels[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại',
            'cancelled' => 'Đã hủy',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now()->toDateString() && !$this->isCompleted();
    }

    /**
     * Scope for completed payments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for overdue payments
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    /**
     * Scope for payments in date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }
}