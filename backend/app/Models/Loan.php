<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Loan Model
 * 
 * Represents active loans in the system
 */
class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_application_id',
        'user_id',
        'loan_id',
        'original_amount',
        'remaining_amount',
        'term',
        'remaining_term',
        'interest_rate',
        'monthly_payment',
        'start_date',
        'next_payment_date',
        'status',
        'is_overdue',
        'days_past_due',
        'penalty_amount',
        'total_paid',
        'payment_schedule',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'start_date' => 'date',
        'next_payment_date' => 'date',
        'payment_schedule' => 'array',
        'is_overdue' => 'boolean',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($loan) {
            if (empty($loan->start_date)) {
                $loan->start_date = now()->toDateString();
            }
            if (empty($loan->next_payment_date)) {
                $loan->next_payment_date = now()->addMonth()->toDateString();
            }
        });

        static::updating(function ($loan) {
            // Update overdue status
            $loan->updateOverdueStatus();
        });
    }

    /**
     * Get the loan application
     */
    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * Get the user that owns the loan
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get payments for this loan
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if loan is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if loan is overdue
     */
    public function isOverdue(): bool
    {
        return $this->is_overdue;
    }

    /**
     * Check if loan is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Update overdue status
     */
    public function updateOverdueStatus(): void
    {
        $today = Carbon::today();
        $nextPaymentDate = Carbon::parse($this->next_payment_date);
        
        if ($today->gt($nextPaymentDate) && $this->isActive()) {
            $this->is_overdue = true;
            $this->days_past_due = $today->diffInDays($nextPaymentDate);
            
            // Calculate penalty
            $penaltyRate = config('loan.penalty_rate', 3.0) / 100;
            $this->penalty_amount = $this->monthly_payment * $penaltyRate * $this->days_past_due;
            
            if ($this->days_past_due > 90) {
                $this->status = 'defaulted';
            } else {
                $this->status = 'overdue';
            }
        } else {
            $this->is_overdue = false;
            $this->days_past_due = 0;
            if ($this->status === 'overdue') {
                $this->status = 'active';
            }
        }
    }

    /**
     * Process payment for this loan
     */
    public function processPayment(float $amount): array
    {
        $interestAmount = $this->remaining_amount * ($this->interest_rate / 100 / 12);
        $principalAmount = min($amount - $interestAmount, $this->remaining_amount);
        
        if ($principalAmount < 0) {
            $principalAmount = 0;
            $interestAmount = $amount;
        }

        $this->remaining_amount -= $principalAmount;
        $this->total_paid += $amount;
        $this->remaining_term--;

        // Update next payment date
        $this->next_payment_date = Carbon::parse($this->next_payment_date)->addMonth();

        // Check if loan is completed
        if ($this->remaining_amount <= 0 || $this->remaining_term <= 0) {
            $this->status = 'completed';
            $this->remaining_amount = 0;
            $this->remaining_term = 0;
        }

        // Reset overdue status if payment is made
        $this->is_overdue = false;
        $this->days_past_due = 0;
        $this->penalty_amount = 0;

        $this->save();

        return [
            'principal_amount' => $principalAmount,
            'interest_amount' => $interestAmount,
            'penalty_amount' => 0, // Reset after payment
        ];
    }

    /**
     * Generate payment schedule
     */
    public function generatePaymentSchedule(): array
    {
        $schedule = [];
        $remainingBalance = $this->original_amount;
        $monthlyRate = $this->interest_rate / 100 / 12;
        
        for ($month = 1; $month <= $this->term; $month++) {
            $interestPayment = $remainingBalance * $monthlyRate;
            $principalPayment = $this->monthly_payment - $interestPayment;
            $remainingBalance -= $principalPayment;
            
            $schedule[] = [
                'month' => $month,
                'principal_payment' => round($principalPayment, 2),
                'interest_payment' => round($interestPayment, 2),
                'total_payment' => round($this->monthly_payment, 2),
                'remaining_balance' => round(max(0, $remainingBalance), 2),
                'due_date' => Carbon::parse($this->start_date)->addMonths($month)->toDateString(),
            ];
        }
        
        return $schedule;
    }

    /**
     * Scope for active loans
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for overdue loans
     */
    public function scopeOverdue($query)
    {
        return $query->where('is_overdue', true);
    }

    /**
     * Scope for completed loans
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for loans due soon
     */
    public function scopeDueSoon($query, int $days = 7)
    {
        return $query->where('next_payment_date', '<=', now()->addDays($days))
                    ->where('status', 'active');
    }
}