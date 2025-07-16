<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Document Resource
 * 
 * Formats document data for API responses
 */
class DocumentResource extends JsonResource
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
            'documentId' => $this->document_id,
            'userId' => $this->user_id,
            'loanApplicationId' => $this->loan_application_id,
            'filename' => $this->filename,
            'originalFilename' => $this->original_filename,
            'filePath' => $this->file_path,
            'fileUrl' => $this->file_url,
            'type' => $this->type,
            'typeLabel' => $this->getTypeLabel(),
            'mimeType' => $this->mime_type,
            'fileSize' => $this->file_size,
            'fileSizeHuman' => $this->file_size_human,
            'status' => $this->status,
            'statusLabel' => $this->getStatusLabel(),
            'verificationNotes' => $this->verification_notes,
            'verifiedBy' => $this->verified_by,
            'verifiedAt' => $this->when($this->verified_at, $this->verified_at->toISOString()),
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
            'pending' => 'Chờ xác thực',
            'verified' => 'Đã xác thực',
            'rejected' => 'Từ chối',
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
}