<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Resource
 * 
 * Formats user data for API responses
 */
class UserResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'username' => $this->when($this->role === 'admin', $this->username),
            'idCard' => $this->when($this->role === 'customer', $this->id_card),
            'address' => $this->when($this->role === 'customer', $this->address),
            'dateOfBirth' => $this->when($this->role === 'customer' && $this->date_of_birth, $this->date_of_birth->format('Y-m-d')),
            'occupation' => $this->when($this->role === 'customer', $this->occupation),
            'monthlyIncome' => $this->when($this->role === 'customer', $this->monthly_income),
            'role' => $this->role,
            'status' => $this->status,
            'verificationStatus' => $this->when($this->role === 'customer', $this->verification_status),
            'creditScore' => $this->when($this->role === 'customer', $this->credit_score),
            'avatarUrl' => $this->avatar_url,
            'permissions' => $this->when($this->role === 'admin', $this->permissions ?? []),
            'walletBalance' => $this->when($this->role === 'customer', $this->wallet_balance),
            'currentDebt' => $this->when($this->role === 'customer', $this->current_debt),
            'totalLoans' => $this->when($this->role === 'customer', $this->total_loans),
            'lastActivity' => $this->when($this->last_activity, $this->last_activity->toISOString()),
            'createdAt' => $this->created_at->toISOString(),
            'updatedAt' => $this->updated_at->toISOString(),
        ];
    }
}