<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Loan Application Resource
 * 
 * Formats loan application data for API responses
 */
class LoanApplicationResource extends JsonResource
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
            'user' => $this->whenLoaded('user', new UserResource($this->user)),
            'amount' => $this->amount,
            'term' => $this->term,
            'purpose' => $this->purpose,
            'status' => $this->status,
            'statusLabel' => $this->getStatusLabel(),
            'verificationStatus' => $this->verification_status,
            'verificationStatusLabel' => $this->getVerificationStatusLabel(),
            'approvedAmount' => $this->approved_amount,
            'approvedTerm' => $this->approved_term,
            'interestRate' => $this->interest_rate,
            'monthlyPayment' => $this->monthly_payment,
            'notes' => $this->notes,
            'conditions' => $this->conditions,
            'emergencyContact' => $this->emergency_contact,
            'priority' => $this->priority,
            'priorityLabel' => $this->getPriorityLabel(),
            'approvedBy' => $this->approved_by,
            'approvedAt' => $this->when($this->approved_at, $this->approved_at->toISOString()),
            'rejectedBy' => $this->rejected_by,
            'rejectedAt' => $this->when($this->rejected_at, $this->rejected_at->toISOString()),
            'rejectionReason' => $this->rejection_reason,
            'rejectionSuggestions' => $this->rejection_suggestions,
            'documents' => $this->whenLoaded('documents', DocumentResource::collection($this->documents)),
            'loan' => $this->whenLoaded('loan', new LoanResource($this->loan)),
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
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'cancelled' => 'Đã hủy',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get verification status label
     */
    private function getVerificationStatusLabel(): string
    {
        $labels = [
            'pending' => 'Chờ xác thực',
            'verified' => 'Đã xác thực',
            'rejected' => 'Xác thực thất bại',
        ];
        
        return $labels[$this->verification_status] ?? $this->verification_status;
    }

    /**
     * Get priority label
     */
    private function getPriorityLabel(): string
    {
        $labels = [
            'low' => 'Thấp',
            'medium' => 'Trung bình',
            'high' => 'Cao',
        ];
        
        return $labels[$this->priority] ?? $this->priority;
    }
}