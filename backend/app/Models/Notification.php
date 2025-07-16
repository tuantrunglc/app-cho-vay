<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification Model
 * 
 * Represents notifications sent to users
 */
class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'read',
        'data',
        'channel',
        'sent',
        'sent_at',
        'read_at',
        'scheduled_at',
    ];

    protected $casts = [
        'read' => 'boolean',
        'sent' => 'boolean',
        'data' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    /**
     * Get the user this notification belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->read;
    }

    /**
     * Check if notification is sent
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->read) {
            $this->update([
                'read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Mark notification as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'sent' => true,
            'sent_at' => now(),
        ]);
    }

    /**
     * Get notification type label
     */
    public function getTypeLabel(): string
    {
        $labels = [
            'loan_approved' => 'Đơn vay được duyệt',
            'loan_rejected' => 'Đơn vay bị từ chối',
            'payment_reminder' => 'Nhắc nhở thanh toán',
            'payment_overdue' => 'Quá hạn thanh toán',
            'system' => 'Thông báo hệ thống',
            'promotion' => 'Khuyến mãi',
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    /**
     * Create loan approval notification
     */
    public static function createLoanApprovalNotification(User $user, LoanApplication $loanApplication): self
    {
        return self::create([
            'user_id' => $user->id,
            'title' => 'Đơn vay được duyệt',
            'content' => "Đơn vay {$loanApplication->loan_id} của bạn đã được duyệt với số tiền " . number_format($loanApplication->approved_amount) . " VND",
            'type' => 'loan_approved',
            'data' => [
                'loan_application_id' => $loanApplication->id,
                'loan_id' => $loanApplication->loan_id,
                'approved_amount' => $loanApplication->approved_amount,
            ],
        ]);
    }

    /**
     * Create loan rejection notification
     */
    public static function createLoanRejectionNotification(User $user, LoanApplication $loanApplication): self
    {
        return self::create([
            'user_id' => $user->id,
            'title' => 'Đơn vay bị từ chối',
            'content' => "Đơn vay {$loanApplication->loan_id} của bạn đã bị từ chối. Lý do: {$loanApplication->rejection_reason}",
            'type' => 'loan_rejected',
            'data' => [
                'loan_application_id' => $loanApplication->id,
                'loan_id' => $loanApplication->loan_id,
                'rejection_reason' => $loanApplication->rejection_reason,
            ],
        ]);
    }

    /**
     * Create payment reminder notification
     */
    public static function createPaymentReminderNotification(User $user, Loan $loan): self
    {
        return self::create([
            'user_id' => $user->id,
            'title' => 'Nhắc nhở thanh toán',
            'content' => "Bạn có khoản thanh toán đến hạn vào ngày {$loan->next_payment_date->format('d/m/Y')} với số tiền " . number_format($loan->monthly_payment) . " VND",
            'type' => 'payment_reminder',
            'data' => [
                'loan_id' => $loan->id,
                'loan_code' => $loan->loan_id,
                'amount' => $loan->monthly_payment,
                'due_date' => $loan->next_payment_date,
            ],
        ]);
    }

    /**
     * Create payment overdue notification
     */
    public static function createPaymentOverdueNotification(User $user, Loan $loan): self
    {
        return self::create([
            'user_id' => $user->id,
            'title' => 'Quá hạn thanh toán',
            'content' => "Khoản vay {$loan->loan_id} của bạn đã quá hạn {$loan->days_past_due} ngày. Vui lòng thanh toán sớm để tránh phí phạt.",
            'type' => 'payment_overdue',
            'data' => [
                'loan_id' => $loan->id,
                'loan_code' => $loan->loan_id,
                'days_past_due' => $loan->days_past_due,
                'penalty_amount' => $loan->penalty_amount,
            ],
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope for sent notifications
     */
    public function scopeSent($query)
    {
        return $query->where('sent', true);
    }

    /**
     * Scope for unsent notifications
     */
    public function scopeUnsent($query)
    {
        return $query->where('sent', false);
    }

    /**
     * Scope for specific notification type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for scheduled notifications
     */
    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
                    ->where('scheduled_at', '<=', now())
                    ->where('sent', false);
    }
}