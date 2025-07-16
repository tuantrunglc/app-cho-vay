<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Payment Resource
 * 
 * Formats payment data for API responses
 */
class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'paymentId' => $this->payment_id,
            'loanId' => $this->loan_id,
            'userId' => $this->user_id,
            'amount' => $this->amount,
            'principalAmount' => $this->principal_amount,
            'interestAmount' => $this->interest_amount,
            'penaltyAmount' => $this->penalty_amount,
            'paymentMethod' => $this->payment_method,
            'paymentMethodLabel' => $this->getPaymentMethodLabel(),
            'status' => $this->status,
            'statusLabel' => $this->getStatusLabel(),
            'paymentDate' => $this->payment_date->format('Y-m-d'),
            'dueDate' => $this->due_date->format('Y-m-d'),
            'notes' => $this->notes,
            'paymentDetails' => $this->payment_details,
            'transactionId' => $this->transaction_id,
            'isOverdue' => $this->isOverdue(),
            'processedAt' => $this->when($this->processed_at, $this->processed_at->toISOString()),
            'completedAt' => $this->when($this->completed_at, $this->completed_at->toISOString()),
            'createdAt' => $this->created_at->toISOString(),
            'updatedAt' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Get status label
     */
    private function getStatusLabel(): string
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
}