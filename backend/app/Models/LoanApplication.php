<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * LoanApplication Model
 * 
 * Represents loan applications submitted by customers
 */
class LoanApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'user_id',
        'amount',
        'term',
        'purpose',
        'status',
        'verification_status',
        'approved_amount',
        'approved_term',
        'interest_rate',
        'monthly_payment',
        'notes',
        'conditions',
        'emergency_contact',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'rejection_suggestions',
        'priority',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'conditions' => 'array',
        'emergency_contact' => 'array',
        'rejection_suggestions' => 'array',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Generate unique loan ID
     */
    public static function generateLoanId(): string
    {
        $lastLoan = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastLoan ? (int)substr($lastLoan->loan_id, 2) + 1 : 1;
        return 'LN' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to auto-generate loan_id
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($loanApplication) {
            if (empty($loanApplication->loan_id)) {
                $loanApplication->loan_id = self::generateLoanId();
            }
        });
    }

    /**
     * Get the user that owns the loan application
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the loan created from this application
     */
    public function loan()
    {
        return $this->hasOne(Loan::class);
    }

    /**
     * Get documents for this loan application
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Check if application is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if application is verified
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Approve the loan application
     */
    public function approve(array $data, string $approvedBy): void
    {
        $this->update([
            'status' => 'approved',
            'approved_amount' => $data['approved_amount'],
            'approved_term' => $data['approved_term'],
            'interest_rate' => $data['interest_rate'],
            'monthly_payment' => $this->calculateMonthlyPayment(
                $data['approved_amount'],
                $data['approved_term'],
                $data['interest_rate']
            ),
            'notes' => $data['notes'] ?? null,
            'conditions' => $data['conditions'] ?? null,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the loan application
     */
    public function reject(array $data, string $rejectedBy): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'rejection_suggestions' => $data['suggestions'] ?? null,
            'rejected_by' => $rejectedBy,
            'rejected_at' => now(),
        ]);
    }

    /**
     * Calculate monthly payment
     */
    private function calculateMonthlyPayment(float $amount, int $term, float $interestRate): float
    {
        $monthlyRate = $interestRate / 100 / 12;
        if ($monthlyRate == 0) {
            return $amount / $term;
        }
        
        return $amount * ($monthlyRate * pow(1 + $monthlyRate, $term)) / (pow(1 + $monthlyRate, $term) - 1);
    }

    /**
     * Scope for pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for verified applications
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    /**
     * Scope for recent applications
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}