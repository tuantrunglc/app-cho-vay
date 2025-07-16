<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Loan Resource
 * 
 * Formats loan data for API responses
 */
class LoanResource extends JsonResource
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
            'loanId' => $this->loan_id,
            'userId' => $this->user_id,
            'loanApplicationId' => $this->loan_application_id,
            'user' => $this->whenLoaded('user', new UserResource($this->user)),
            'originalAmount' => $this->original_amount,
            'remainingAmount' => $this->remaining_amount,
            'term' => $this->term,
            'remainingTerm' => $this->remaining_term,
            'interestRate' => $this->interest_rate,
            'monthlyPayment' => $this->monthly_payment,
            'startDate' => $this->start_date->format('Y-m-d'),
            'nextPaymentDate' => $this->next_payment_date->format('Y-m-d'),
            'status' => $this->status,
            'statusLabel' => $this->getStatusLabel(),
            'isOverdue' => $this->is_overdue,
            'daysPastDue' => $this->days_past_due,
            'penaltyAmount' => $this->penalty_amount,
            'totalPaid' => $this->total_paid,
            'paymentSchedule' => $this->payment_schedule,
            'payments' => $this->whenLoaded('payments', PaymentResource::collection($this->payments)),
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
            'active' => 'Đang hoạt động',
            'completed' => 'Hoàn thành',
            'overdue' => 'Quá hạn',
            'defaulted' => 'Nợ xấu',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
}