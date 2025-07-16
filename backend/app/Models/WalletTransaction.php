<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * WalletTransaction Model
 * 
 * Represents wallet transactions for users
 */
class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'metadata',
        'reference_id',
        'reference_type',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Generate unique transaction ID
     */
    public static function generateTransactionId(): string
    {
        $lastTransaction = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastTransaction ? (int)substr($lastTransaction->transaction_id, 3) + 1 : 1;
        return 'TXN' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate transaction_id
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = self::generateTransactionId();
            }
        });
    }

    /**
     * Get the user this transaction belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if transaction is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if transaction is debit (money out)
     */
    public function isDebit(): bool
    {
        return in_array($this->type, ['withdrawal', 'payment', 'penalty']);
    }

    /**
     * Check if transaction is credit (money in)
     */
    public function isCredit(): bool
    {
        return in_array($this->type, ['deposit', 'loan_disbursement', 'refund']);
    }

    /**
     * Get transaction type label
     */
    public function getTypeLabel(): string
    {
        $labels = [
            'deposit' => 'Nạp tiền',
            'withdrawal' => 'Rút tiền',
            'loan_disbursement' => 'Giải ngân khoản vay',
            'payment' => 'Thanh toán',
            'refund' => 'Hoàn tiền',
            'penalty' => 'Phí phạt',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'Chờ xử lý',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại',
            'cancelled' => 'Đã hủy',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Create deposit transaction
     */
    public static function createDeposit(User $user, float $amount, string $description, array $metadata = []): self
    {
        $currentBalance = $user->wallet_balance;
        
        return self::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance + $amount,
            'description' => $description,
            'metadata' => $metadata,
            'status' => 'completed',
        ]);
    }

    /**
     * Create withdrawal transaction
     */
    public static function createWithdrawal(User $user, float $amount, string $description, array $metadata = []): self
    {
        $currentBalance = $user->wallet_balance;
        
        return self::create([
            'user_id' => $user->id,
            'type' => 'withdrawal',
            'amount' => -$amount,
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance - $amount,
            'description' => $description,
            'metadata' => $metadata,
            'status' => 'completed',
        ]);
    }

    /**
     * Create loan disbursement transaction
     */
    public static function createLoanDisbursement(User $user, Loan $loan): self
    {
        $currentBalance = $user->wallet_balance;
        
        return self::create([
            'user_id' => $user->id,
            'type' => 'loan_disbursement',
            'amount' => $loan->original_amount,
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance + $loan->original_amount,
            'description' => "Giải ngân khoản vay {$loan->loan_id}",
            'reference_id' => $loan->id,
            'reference_type' => 'loan',
            'metadata' => [
                'loan_id' => $loan->loan_id,
                'loan_amount' => $loan->original_amount,
                'interest_rate' => $loan->interest_rate,
                'term' => $loan->term,
            ],
            'status' => 'completed',
        ]);
    }

    /**
     * Create payment transaction
     */
    public static function createPayment(User $user, Payment $payment): self
    {
        $currentBalance = $user->wallet_balance;
        
        return self::create([
            'user_id' => $user->id,
            'type' => 'payment',
            'amount' => -$payment->amount,
            'balance_before' => $currentBalance,
            'balance_after' => $currentBalance - $payment->amount,
            'description' => "Thanh toán khoản vay {$payment->loan->loan_id}",
            'reference_id' => $payment->id,
            'reference_type' => 'payment',
            'metadata' => [
                'payment_id' => $payment->payment_id,
                'loan_id' => $payment->loan->loan_id,
                'principal_amount' => $payment->principal_amount,
                'interest_amount' => $payment->interest_amount,
                'penalty_amount' => $payment->penalty_amount,
            ],
            'status' => 'completed',
        ]);
    }

    /**
     * Scope for completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending transactions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for specific transaction type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for debit transactions
     */
    public function scopeDebits($query)
    {
        return $query->whereIn('type', ['withdrawal', 'payment', 'penalty']);
    }

    /**
     * Scope for credit transactions
     */
    public function scopeCredits($query)
    {
        return $query->whereIn('type', ['deposit', 'loan_disbursement', 'refund']);
    }

    /**
     * Scope for transactions in date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}